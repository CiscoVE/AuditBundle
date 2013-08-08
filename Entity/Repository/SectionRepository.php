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
    public function getSectionOptions()
    {
        $array = array();
        foreach( $this->getSections() as $set )
        {
            if( !$set->getForm() )
            {
                continue;
            }

            if( !array_key_exists( $set->getForm()->getTitle(), $array ))
            {
                $array[$set->getForm()->getTitle()] = array();
            }

            $array[$set->getForm()->getTitle()][$set->getId()] = $set;
        }

        return $array;
    }
}
