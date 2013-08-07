<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class FormRepository extends SortableRepository
{
    public function qbForms( $section, $archived )
    {
        return $this->createQueryBuilder( 'f' )
                    ->join( 'CiscoSystemsAuditBundle:FormSection', 'r', 'with', 'f = r.form' )
                    ->join( 'CiscoSystemsAuditBundle:Section', 's', 'with', 'r.section = s ')
                    ->where( 's = :section' )
                    ->andWhere( 'r.archived = :archived' )
                    ->setParameters( array(
                        'section' => $section,
                        'archived' => $archived
                    ));
    }

    public function getForms( $section, $archived = FALSE )
    {
        return $this->qbFroms( $section, $archived )
                    ->getQuery()
                    ->getResult();
    }
}
