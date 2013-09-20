<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Field;

/**
 * Custom query repository for SFC REview
 */
class SectionRepository extends SortableRepository
{
    /**
     * Return an array of sections grouped by forms
     * see http://stackoverflow.com/questions/13344915/entity-mapping-in-a-symfony2-choice-field-with-optgroup
     *
     * @return array Array of Entities Section
     */
    public function getSectionOptions( Form $form = NULL, $archived = FALSE )
    {
        $array = array();
        foreach( $this->getPerForm( $form, $archived ) as $section )
        {
            if( FALSE !== $section->getForm() )
            {
                if( !$section->getForm() ) { continue; }
                if( !array_key_exists( $section->getForm()->getTitle(), $array ))
                {
                    $array[$section->getForm()->getTitle()] = array();
                }
                $array[$section->getForm()->getTitle()][$section->getId()] = $section;
            }
        }

        return $array;
    }

    /**
     * Returns a query for getting the sections based on the provided Form
     * and archived value.
     *
     * @param \CiscoSystems\AuditBundle\Entity\Form $form
     * @param boolean $archived
     *
     * @return QueryBuilder
     */
    public function qbPerForm( Form $form = NULL, $archived = NULL )
    {
        $qb = $this->createQueryBuilder( 's' );
        if( NULL !== $form || NULL !== $archived )
        {
            $qb->join( 'CiscoSystemsAuditBundle:FormSection', 'r', 'with', 's = r.section' );
        }
        $and = $qb->expr()->andX();
        if( NULL !== $form )
        {
            $qb->join( 'CiscoSystemsAuditBundle:Form', 'f', 'with', 'f = r.form' );
            $and->add( $qb->expr()->eq( 'f', ':form' ));
            $qb->setParameter( 'form', $form );
        }
        if( NULL !== $archived )
        {
            $and->add( $qb->expr()->eq( 'r.archived', ':archived' ));
            $qb->setParameter( 'archived', $archived );
        }
        if( $and->count() > 0 ) $qb->where( $and );

        return $qb;
    }

    public function getPerForm( Form $form = NULL, $archived = NULL )
    {
        return $this->qbPerForm( $form, $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbPerField( Field $field = NULL, $archived = NULL )
    {
        $qb = $this->createQueryBuilder( 's' );
        if( NULL !== $field || NULL !== $archived )
        {
            $qb->join( 'CiscoSystemsAuditBundle:SectionField', 'r', 'with', 's = r.section' );
        }
        $and = $qb->expr()->andX();
        if( NULL !== $field )
        {
            $qb->join( 'CiscoSystemsAuditBundle:Field', 'f', 'with', 'f = r.field' );
            $and->add( $qb->expr()->eq( 'f', ':form' ));
            $qb->setParameter( 'field', $field );
        }
        if( NULL !== $archived )
        {
            $and->add( $qb->expr()->eq( 'r.archived', ':archived' ));
            $qb->setParameter( 'archived', $archived );
        }
        if( $and->count() > 0 ) $qb->where( $and );

        return $qb;
    }

    public function getPerField( Field $field = NULL, $archived = NULL )
    {
        return $this->qbPerField( $field, $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbArchived( $archived )
    {
        return $this->createQueryBuilder( 's' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r1', 'with', 'r1.section = s' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\SectionField', 'r2', 'WITH', 'r2.section = s' )
                    ->where( 'r1.archived = :archived' )
                    ->andWhere( 'r2.archived = :archived' )
                    ->setParameter( 'archived', $archived );
    }

    public function getArchived( $archived )
    {
        return $this->qbArchived( $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbAttached()
    {
        return $this->createQueryBuilder( 's' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r', 'with', 'r.section = s' )
                    ->groupBy( 'r.section' );
    }

    public function getAttached()
    {
        return $this->qbAttached()
                    ->getQuery()
                    ->getResult();
    }

    public function qbDetached( $sections )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's NOT IN ( :sections )' )
                    ->setParameter( 'sections', $sections );
    }

    public function getDetached( $sections )
    {
        return $this->qbDetached( $sections )
                    ->getQuery()
                    ->getResult();
    }

    /**
     * get array of sections not linked to any relation form-section
     *
     * @return ArrayCollection
     */
    public function getDetachedSections()
    {
        return $this->getDetached( $this->getAttached() );
    }

    public function getArchivedSections( $archived = FALSE )
    {
        return $this->getDetached( $this->getArchived( $archived ) );
    }

    /**
     * un-assigned sections:
     *
     * 1. get all sections
     * 2. in there, find sections which are belonging to the current form
     * 3. from these, remove the one for which the relation.archived === false
     *
     *
     * Get all section for form_id = 1
     *
     * SELECT e.id AS section_id, e.title, f.id AS form_id, fs.id AS relation_id, r.archived
     * FROM  `audit__form_section` fs
     * JOIN  `audit__relation` r ON fs.id = r.id
     * JOIN  `audit__form` f ON fs.form_id = f.id
     * JOIN  `audit__section` s ON fs.section_id = s.id
     * JOIN  `audit__element` e ON e.id = s.id
     * WHERE fs.form_id =1
     * LIMIT 0 , 30
     *
     */
    public function getUnAssignedPerForm( Form $form = NULL )
    {
        return ( count( $this->getPerForm( $form, FALSE ) ) > 0 ) ?
               $this->getDetached( $this->getPerForm( $form, FALSE ) ) :
               $this->getPerForm() ;
    }
}
