<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;

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

    public function qbRelationPerForm( Form $form )
    {
        return $this->createQueryBuilder( 'r' )
                    ->where( 'r.form = :form' )
                    ->setParameter( 'form', $form );
    }

    public function getRelationPerForm( Form $form )
    {
        return $this->qbRelationPerForm( $form )
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