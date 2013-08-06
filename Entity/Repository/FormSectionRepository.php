<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class FormSectionRepository extends SortableRepository
{
    public function qbSectionRelation( $form, $section )
    {
        return $this->createQueryBuilder( 'r' )
                    ->join( 'CiscoSystemsAuditBundle:Form', 'f', 'WITH', 'f.formSection = r' )
                    ->join( 'CiscoSystemsAuditBundle:Section', 's', 'WITH', 's.formSection = r' )
                    ->where( 's = :section' )
                    ->andWhere( 'f = :form' )
                    ->addParameters( array(
                        'section'   => $section,
                        'form'      => $form,
                    ));
    }

    public function getSectionRelation( $form, $section )
    {
        return $this->qbSectionRelation( $form, $section )
                    ->getQuery()
                    ->getResult();
    }
}