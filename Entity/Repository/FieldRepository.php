<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Custom query repository for SFC REview
 */
class FieldRepository extends SortableRepository
{
    /**
     * Get the flag label for the given Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return string
     */
    public function getTrigger( $field )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'form.flagLabel' )->from( 'CiscoSystemsAuditBundle:Field',  'field' );
        $qb->join( 'CiscoSystemsAuditBundle:Section', 'section', 'with', 'section = field.section' );
        $qb->join( 'CiscoSystemsAuditBundle:Form', 'form', 'with', 'form = section.form' );
        $qb->add( 'where', $qb->expr()->eq( 'field', ':field' ));
        $qb->setParameter( 'field', $field );
        $array = $qb->getQuery()->getOneOrNullResult();

        return $array['flagLabel'];
    }
}
