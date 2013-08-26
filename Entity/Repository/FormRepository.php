<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class FormRepository extends SortableRepository
{
    public function qbForms( $section, $archived )
    {
        return $this->createQueryBuilder( 'f' )
                    ->join( 'CiscoSystemsAuditBundle:FormSection', 'r', 'with', 'f = r.form' )
                    ->join( 'CiscoSystemsAuditBundle:Section', 's', 'with', 'r.section = s')
                    ->where( 's = :section' )
                    ->andWhere( 'r.archived = :archived' )
                    ->setParameters( array(
                        'section' => $section,
                        'archived' => $archived
                    ));
    }

    public function getForms( $section, $archived = FALSE )
    {
        return $this->qbForms( $section, $archived )
                    ->getQuery()
                    ->getResult();
    }

    public function qbFormState( array $index )
    {
        return $this->createQueryBuilder( 'fo' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Audit', 'a', 'WITH', 'fo = a.form' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'fs', 'WITH', 'fo = fs.form' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Section', 's', 'WITH', 's = fs.section' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\SectionField', 'sf', 'WITH', 's = sf.section' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Field', 'fi', 'WITH', 'fi = sf.field' )
                    ->where( 'fo.id = :id' )
                    ->andWhere( 's.id IN ( :sections )' )
                    ->andWhere( 'fi.id IN ( :fields )' )
                    ->setParameters( array(
                        'id'         => reset( $index['forms'] ),
                        'sections'   => implode( "', '", $index['sections'] ),
                        'fields'     => implode( "', '", $index['fields'] ),
                    ));
    }

    public function qbAudit( $id )
    {
        return $this->createQueryBuilder( 'f' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Audit', 'a', 'WITH', 'f = a.form' )
                    ->where( 'a.id = :id' )
                    ->setParameter( 'id', $id );
    }

    public function getFormState( $id )
    {
        $form = $this->qbAudit( $id )
                      ->getQuery()
                      ->getResult();
        $state = reset( $form );//->getFormState();

        // build the index of used form, section and fields for the audit
        $index = array(
            'forms'     => array(),
            'sections'  => array(),
            'fields'    => array()
        );

        foreach( $state as $form )
        {
            array_push( $index['forms'], $form['id'] );
            foreach( $form['sections'] as $section )
            {
                array_push( $index['sections'], $section['id'] );
                foreach( $section['fields'] as $field )
                {
                    array_push( $index['fields'], $field );
                }
            }
        }

        $result = $this->qbFormState( $index )
                       ->getQuery()
                       ->getResult();

        return reset( $result );
    }
}
