<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class AuditRepository extends EntityRepository
{
    public function getLastAudits( $limit = 10 )
    {
        $qb = $this->createQueryBuilder( 'a' );
        $qb->addOrderBy( 'a.createdAt', 'desc' );
        $qb->setMaxResults( $limit );
        return $qb->getQuery()->execute();
    }

    public function qbAudits( $auditor = null )
    {
        $qb = $this->createQueryBuilder( 'a' );
        if( $auditor !== null )
        {
            $qb->add( 'where', $qb->expr()->eq( 'a.auditor', ':user' ))
               ->setParameter( 'user', $auditor );
        }

        return $qb;
    }

    public function getAuditsPerAuditor( $auditor = null )
    {
        return $this->qbAudits( $auditor )->getQuery()->getResult();
    }

    /**
     * get audits as an array with the auditforms used
     *
     * @return array
     */
    public function getAuditsWithFormsUsage( $auditor = null )
    {
        $entities = $this->qbAudits( $auditor )->getQuery()->getResult();

        $uforms = array();
        $result = array();

        // find all used forms
        foreach ( $entities as $entity )
        {
            $refId = $entity->getReference()->getId();
            $formId = $entity->getForm()->getId();

            // if key $refId does NOT exist in uforms
            if( !array_key_exists( $refId, $uforms ) )
            {
                $uforms[$refId] = array( $formId );
            }
            else
            {
                // if value $formId does NOT exist in $uforms[$refId]
                if( !in_array( $formId, $uforms[$refId] ))
                {
                    // add value $formId to $uforms[$refId]
                    array_push($uforms[$refId], $formId);
                }
            }
        }

        foreach ( $entities as $entity )
        {
            $form = $entity->getForm();
            $metadata = $form->getMetadata();
            $accessLevel = $metadata ? $metadata->getAccessLevel() : 1;
            $row = array(
                'id'            => $entity->getId(),
                'reference'     => $entity->getReference()->getId(),
                'form'          => $entity->getForm()->getTitle(),
                'auditor'       => $entity->getAuditor()->getUsername(),
                'flag'          => $entity->getFlag(),
                'flagLabel'     => $entity->getForm()->getFlagLabel(),
                'mark'          => $entity->getMark(),
                'usedforms'     => $uforms[$entity->getReference()->getId()],
                'createdAt'     => $entity->getCreatedAt(),
                'accessLevel'   => $accessLevel,
            );
            $result[] = $row;
        }

        return $result;
    }

    public function qbReferences()
    {
        return $this->createQueryBuilder( 'c' )
                    ->select( 'IDENTITY( c.reference )' )
                    ->orderBy( 'c.reference', 'DESC' );
    }

    /**
     * Get an array of all reference
     *
     * @return array reference
     */
    public function getCaseId()
    {
        $qb = $this->qbReferences()
                   ->getQuery();
        $return = array();
        foreach( $qb->getScalarResult() as $id )
        {
            foreach( $id as $v ) { $return[] = intval( $v ); }
        }

        return $return;
    }

    public function qbAuditByFormAndReference( $form, $reference )
    {
        return $this->createQueryBuilder( 'a' )
                    ->join( 'CiscoSystems\AuditBundle\Model\ReferenceInterface', 'r', 'with', 'a.reference = r' )
                    ->where( 'a.form = :form' )
                    ->andWhere( 'r.id = :refid' )
                    ->setParameters( array(
                        'form'      => $form,
                        'refid'     => $reference
                    ));
    }

    public function getAuditByFormAndReference( $form, $refId )
    {
        return $this->qbAuditByFormAndReference( $form, $refId )
                    ->getQuery()
                    ->getResult();
    }

    public function qbFormState( array $index )
    {
        return $this->createQueryBuilder( 'a' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Form', 'fo', 'WITH', 'fo = a.form' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\FormSection', 'fs', 'WITH', 'fo = fs.form' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Section', 's', 'WITH', 's = fs.section' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\SectionField', 'sf', 'WITH', 's = sf.section' )
                    ->join( 'CiscoSystems\AuditBundle\Entity\Field', 'fi', 'WITH', 'fi = sf.field' )
                    ->where( 'a.id = :id' )
                    ->andWhere( 's.id IN ( :sections )' )
                    ->andWhere( 'fi.id IN ( :fields )' )
                    ->setParameters( array(
                        'id'         => reset( $index['forms'] ),
                        'sections'   => implode( "', '", $index['sections'] ),
                        'fields'     => implode( "', '", $index['fields'] ),
                    ));
    }

    public function getFormState( $id )
    {

        $qb = $this->createQueryBuilder( 'a' )
                   ->where( 'a.id = :id' )
                   ->setParameter( 'id', $id );
        $audit = $qb->getQuery()->getResult();
        $state = reset( $audit )->getFormState();

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