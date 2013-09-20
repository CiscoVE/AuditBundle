<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;

class FormRepository extends SortableRepository
{
    public function qbPerSection( Section $section, $archived )
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

    public function getPerSection( Section $section, $archived = FALSE )
    {
        return $this->qbForms( $section, $archived )
                    ->getQuery()
                    ->getResult();
    }

    /**
     * WARNING: this is a test method. The intention is to retrieve Form-Section-Field
     * associated Entities for the given parameters
     *
     * @param type $id
     * @param array $index
     * @return type
     */
    public function qbState( $id, array $index )
    {
        return $this->createQueryBuilder( 'fo' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Audit', 'a', 'WITH', 'fo = a.form' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\FormSection', 'fs', 'WITH', 'fo = fs.form' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Section', 's', 'WITH', 's = fs.section' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\SectionField', 'sf', 'WITH', 's = sf.section' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Field', 'fi', 'WITH', 'fi = sf.field' )
                    ->where( 'a.id = :id' )
                    ->andwhere( 'fo.id = :form' )
                    ->andWhere( 's.id IN ( :sections )' )
                    ->andWhere( 'fi.id IN ( :fields )' )
                    ->setParameters( array(
                        'id'         => $id,
                        'form'           => reset( $index['forms'] ),
                        'sections'   => $index['sections'],
                        'fields'     => $index['fields'],
                    ));
    }

    public function getState( Audit $audit )
    {
        $index = $audit->getFormIndexes();
        $id = $audit->getId();
        $result = $this->qbState( $id, $index )
                       ->getQuery()
                       ->getResult();

        return reset( $result );
    }

    public function qbArchived( $archived = FALSE )
    {
        return $this->createQueryBuilder( 'f' )
                    ->innerJoin( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r', 'WITH', 'r.form = f')
                    ->where( 'r.archived = :archived' )
                    ->setParameter( 'archived', $archived );
    }

    public function getArchived( $archived = FALSE )
    {
        return $this->qbArchived( $archived )
                    ->getQuery()
                    ->getResult();
    }
}
