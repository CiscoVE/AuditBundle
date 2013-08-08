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

    public function getDetachedFields()
    {
        $fields = $this->qbAttached()
                       ->getQuery()
                       ->getResult();

        return $this->qbDetached( $fields )
                    ->getQuery()
                    ->getResult();
    }
}