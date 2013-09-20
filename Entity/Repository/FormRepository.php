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
     * possible solution to be explored:
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/working-with-indexed-associations.html
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/extra-lazy-associations.html
     *
     * @param type $id
     * @param array $index
     * @return type
     */
    public function qbState( Audit $audit )
    {
        $index = $audit->getFormIndexes();

        return $this->createQueryBuilder( 'fo' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Audit', 'a', 'WITH', 'fo = a.form' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\FormSection', 'fs', 'WITH', 'fo = fs.form' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Section', 's', 'WITH', 's = fs.section' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\SectionField', 'sf', 'WITH', 's = sf.section' )
                    ->innerjoin( 'CiscoSystems\AuditBundle\Entity\Field', 'fi', 'WITH', 'fi = sf.field' )
                    ->where( 'a = :audit' )
                    ->andwhere( 'fo.id = :form' )
                    ->andWhere( 's.id IN ( :sections )' )
                    ->andWhere( 'fi.id IN ( :fields )' )
                    ->setParameters( array(
                        'audit'      => $audit,
                        'form'       => reset( $index['forms'] ),
                        'sections'   => $index['sections'],
                        'fields'     => $index['fields'],
                    ));
    }

    public function getState( Audit $audit )
    {
        $result = $this->qbState( $audit )
                       ->getQuery()
                       ->getResult();

        return reset( $result );
    }

    /**
     * Trying to be a smart arse, but Doctrine said no, so far .... -_-''
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function qbArchived( $archived = TRUE )
    {
        return $this->createQueryBuilder( 'f' )
                    ->innerJoin( 'CiscoSystems\AuditBundle\Entity\FormSection', 'r', 'WITH', 'r.form = f')
                    ->where( 'r.archived = :archived' )
                    ->setParameter( 'archived', $archived );
    }

    /**
     * Yeah, well there is now a $form->isArchived() method that seems to do
     * this job already for individual forms and it works .... <_<
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getArchived( $archived = TRUE )
    {
        $ret = array();
        foreach( $this->findAll() as $form )
        {
            if( $form->getSections() > $form->getSections( $archived ) )
            {
                $ret[] = $form;
            }
        }

        return $ret;
    }
}
