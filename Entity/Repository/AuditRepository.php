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
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->select( 'a' )->from( 'CiscoSystemsAuditBundle:Audit', 'a' );
//        if( $auditor !== null )
//        {
//            $qb->add( 'where', $qb->expr()->eq( 'a.auditor', ':user' ));
//            $qb->setParameter( 'user', $auditor );
//        }
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
            $formAccessLevel = $metadata ? $metadata->getAccessLevel() : 1;
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
                'accessLevel'   => $formAccessLevel,
            );
            $result[] = $row;
        }
        return $result;
    }

    public function qbReferences()
    {
        return $this->createQueryBuilder( 'c' )
                    ->select( 'c.reference' )
                    ->from( 'CiscoSystemsAuditBundle:Audit', 'c' )
                    ->orderBy( 'c.reference', 'DESC' );
    }

    /**
     * Get an array of all reference
     *
     * @return array reference
     */
    public function getCaseId()
    {
        $qb = $this->getEntityManager()->createQuery( '
                    SELECT IDENTITY( c.reference )
                    FROM CiscoSystemsAuditBundle:Audit c
                    ORDER BY c.reference DESC
                ');

        $return = array();
        foreach( $qb->getScalarResult() as $id )
        {
            foreach( $id as $v ) { $return[] = intval( $v ); }
        }

        return $return;
    }
}