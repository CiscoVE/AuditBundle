<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class SectionRepository extends SortableRepository
{
    public function getSections( $auditform = null )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'section' )->from( 'CiscoSystemsAuditBundle:Section', 'section' );
//        $qb->join( 'CiscoSystemsAuditBundle:Form', 'auditform', 'with', 'formsection.form = auditform' );
        if( $auditform !== null )
        {
            $qb->add( 'where', $qb->expr()->eq( 'form', ':form' ));
            $qb->setParameter( 'form', $auditform );
        }

        return $qb->getQuery()->getResult();
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
        $sections  = $this->getEntityManager()
                ->getRepository( 'CiscoSystemsAuditBundle:Section' )
                ->getQuery()->getResult();

        foreach( $sections as $set )
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
