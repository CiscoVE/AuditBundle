<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class SectionRepository extends SortableRepository
{
    public function qbSections( $form = NULL )
    {
        $qb = $this->createQueryBuilder( 's' );
        if( $form !== null )
        {
            $qb->join( 'CiscoSystemsAuditBundle:FormSection', 'r', 'with', 's = r.section' )
               ->join( 'CiscoSystemsAuditBundle:Form', 'f', 'with', 'f = r.form' )
               ->add( 'where', $qb->expr()->eq( 'f', ':form' ))
               ->setParameter( 'form', $form );
        }

        return $qb;
    }

    public function getSections( $form = NULL )
    {
        return $this->qbSections( $form )
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Return an array of sections grouped by forms
     * see http://stackoverflow.com/questions/13344915/entity-mapping-in-a-symfony2-choice-field-with-optgroup
     *
     * @return array Array of Entities Section
     */
    public function getSectionOptions( $form = NULL )
    {
        $array = array();
        $sections = $this->getSections( $form );
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
}
