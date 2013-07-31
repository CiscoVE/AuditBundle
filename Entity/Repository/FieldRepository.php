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
}
