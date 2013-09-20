<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class FieldRepository extends SortableRepository
{
    public function qbTrigger( $field )
    {
        return $this->createQueryBuilder( 'fo.flagLabel' )
                    ->from( 'CiscoSystemsAuditBundle:Field',  'fi' )
                    ->join( 'CiscoSystemsAuditBundle:Section', 's', 'with', 's = fi.section' )
                    ->join( 'CiscoSystemsAuditBundle:Form', 'fo', 'with', 'fo = s.form' )
                    ->where( 'fi = :field' )
                    ->setParameter( 'field', $field );
    }

    /**
     * Get the flag label for the given Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return string
     */
    public function getTrigger( $field )
    {
        $array = $this->qbTrigger( $field )
                      ->getQuery()
                      ->getOneOrNullResult();

        return $array['flagLabel'];
    }

    public function qbFields( $section = NULL, $archived = NULL )
    {
        $qb = $this->createQueryBuilder( 'f' );
        if( NULL !== $section || NULL !== $archived )
        {
            $qb->join( 'CiscoSystemsAuditBundle:SectionField', 'r', 'with', 'f = r.field' );
        }
        $and = $qb->expr()->andX();
        if( NULL !== $section )
        {
            $qb->join( 'CiscoSystemsAuditBundle:Section', 's', 'with', 's = r.section' );
            $and->add( $qb->expr()->eq( 's', ':section' ));
            $qb->setParameter( 'section', $section );
        }
        if( NULL !== $archived )
        {
            $and->add( $qb->expr()->eq( 'r.archived', ':archived' ));
            $qb->setParameter( 'archived', $archived );
        }
        if( $and->count() > 0 ) $qb->where( $and );

        return $qb;
    }

    public function getFields( $section = NULL, $archived = NULL )
    {
        return $this->qbFields( $section, $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbArchived( $archived )
    {
        return $this->createQueryBuilder( 'f' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\SectionField', 'r', 'with', 'r.field = f' )
                    ->where( 'r.archived = :archived' )
                    ->setParameter( 'archived', $archived );
    }

    public function qbAttached()
    {
        return $this->createQueryBuilder( 'f' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\SectionField', 'r', 'with', 'r.field = f' )
                    ->groupBy( 'r.field' );
    }

    public function qbDetached( $fields )
    {
        return $this->createQueryBuilder( 'f' )
                    ->where( 'f NOT IN ( :fields )' )
                    ->setParameter( 'fields', $fields );
    }

    public function getDetached( $fields )
    {
        return $this->qbDetached( $fields )
                    ->getQuery()
                    ->getResult();
    }

    public function getDetachedFields()
    {
        $fields = $this->qbAttached()
                       ->getQuery()
                       ->getResult();

        return $this->qbDetached( $fields )
                    ->getQuery()
                    ->getResult();
    }

    public function getArchivedFields( $archived = FALSE )
    {
        $fields = $this->qbArchived( $archived )
                       ->getQuery()
                       ->getResult();

        return $this->qbDetached( $fields )
                    ->getQuery()
                    ->getResult();
    }

    /**
     * get all fields that are not included in relation section - field AND
     * all fields that are in relation section - field AND for which the
     * relation.archived === TRUE
     *
     * @param type $section
     * @return type
     *
     * SELECT *
     * FROM  `audit__relation` r
     * JOIN  `audit__section_field` sf ON r.id = sf.id
     * JOIN  `audit__field` f ON sf.field_id = f.id
     * JOIN  `audit__element` e ON f.id = e.id
     * LIMIT 0 , 30
     *
     */
    public function getUnAssignedFields( $section = NULL )
    {
        return ( count( $this->getFields( $section, FALSE ) ) > 0 ) ?
               $this->getDetached( $this->getFields( $section, FALSE ) ) :
               $this->getFields() ;
    }
}