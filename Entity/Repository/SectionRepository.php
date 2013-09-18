<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

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
    public function getSectionOptions( $form = NULL, $archived = FALSE )
    {
        $array = array();
        $sections = $this->getSections( $form, $archived );

        foreach( $sections as $section )
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
    public function qbSections( $form = NULL, $archived = NULL )
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

    public function getSections( $form = NULL, $archived = NULL )
    {
        return $this->qbSections( $form, $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbArchived( $archived )
    {
        return $this->createQueryBuilder( 's' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r', 'with', 'r.section = s' )
                    ->where( 'r.archived = :archived' )
                    ->setParameter( 'archived', $archived );
    }

    public function qbAttached()
    {
        return $this->createQueryBuilder( 's' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r', 'with', 'r.section = s' )
                    ->groupBy( 'r.section' );
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
        $sections = $this->qbAttached()
                         ->getQuery()
                         ->getResult();

        return $this->qbDetached( $sections )
                    ->getQuery()
                    ->getResult();
    }

    public function getArchivedSections()
    {
        $sections = $this->qbArchived( FALSE )
                         ->getQuery()
                         ->getResult();

        return $this->qbDetached( $sections )
                    ->getQuery()
                    ->getResult();
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
    public function getUnAssignedSections( $form = NULL )
    {
        return ( count( $this->getSections( $form, FALSE ) ) > 0 ) ?
               $this->getDetached( $this->getSections( $form, FALSE ) ) :
               $this->getSections() ;
    }
}
