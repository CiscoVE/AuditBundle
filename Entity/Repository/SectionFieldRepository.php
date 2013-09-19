<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Field;

class SectionFieldRepository extends SortableRepository
{
    public function qbRelationPerField( Field $field )
    {
        return $this->createQueryBuilder( 'r' )
                    ->where( 'r.field = :field' )
                    ->setParameter( 'field' , $field );
    }

    public function getRelationPerField( Field $field )
    {
        return $this->qbRelationPerField( $field )
                    ->getQuery()
                    ->getResult();
    }

    public function qbRelationPerSection( Section $section )
    {
        return $this->createQueryBuilder( 'r' )
                    ->where( 'r.section = :section' )
                    ->setParameter( 'section', $section );
    }

    public function getRelationPerSection( Section $section )
    {
        return $this->qbRelationPerSection( $section )
                    ->getQuery()
                    ->getResult();
    }
}