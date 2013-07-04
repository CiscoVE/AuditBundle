<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class AuditFormSectionRepository extends SortableRepository
{
    public function getSections( $auditform = null )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'formsection' )->from( 'CiscoSystemsAuditBundle:AuditFormsection', 'formsection' );
//        $qb->join( 'CiscoSystemsAuditBundle:AuditForm', 'auditform', 'with', 'formsection.form = auditform' );
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
     * @return array Array of Entities AuditFormSection
     */
    public function getSectionOptions()
    {
        $array = array();
        $sections  = $this->getEntityManager()
                ->getRepository( 'CiscoSystemsAuditBundle:AuditFormsection' )
                ->getQuery()->getResult();

        foreach( $sections as $set )
        {
            if( !$set->getAuditForm() )
            {
                continue;
            }

            if( !array_key_exists( $set->getAuditForm()->getTitle(), $array ))
            {
                $array[$set->getAuditForm()->getTitle()] = array();
            }

            $array[$set->getAuditForm()->getTitle()][$set->getId()] = $set;
        }

        return $array;
    }
}
