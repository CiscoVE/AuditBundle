<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class AuditFormFieldRepository extends SortableRepository
{
    /**
     * Get the flag label for the given AuditFormField
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormField $field
     *
     * @return string
     */
    public function getTrigger( $field )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'form.flagLabel' )->from( 'CiscoSystemsAuditBundle:AuditFormField',  'auditfield' );
        $qb->join( 'CiscoSystemsAuditBundle:AuditFormSection', 'auditsection', 'with', 'auditsection = auditfield.section' );
        $qb->join( 'CiscoSystemsAuditBundle:AuditForm', 'form', 'with', 'form = auditsection.auditForm' );
        $qb->add( 'where', $qb->expr()->eq( 'auditfield', ':field' ));
        $qb->setParameter( 'field', $field );

//        $array = $qb->getQuery()->getScalarResult();
        $array = $qb->getQuery()->getOneOrNullResult();
        $ret = '';

        foreach( $array as $value )
        {
            $ret .= $value;
        }

        return $ret;
    }

}
