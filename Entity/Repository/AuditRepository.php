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

    /**
     * get audits as an array with the auditforms used
     *
     * @return array
     */
    public function getAuditsWithAuditFormsUsage()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'a' )->from( 'CiscoSystemsAuditBundle:Audit', 'a' );
        $entities = $qb->getQuery()->getResult();

        $uforms = array();
        $result = array();

        // find all used forms
        foreach ( $entities as $entity )
        {
            $refId = $entity->getAuditReference()->getId();
            $formId = $entity->getAuditForm()->getId();

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
            $row = array(
                'id' => $entity->getId(),
                'auditReference' => $entity->getAuditReference()->getId(),
                'auditForm' => $entity->getAuditForm()->getTitle(),
                'auditingUser' => $entity->getAuditingUser()->getUsername(),
                'flag' => $entity->getFlag(),
                'flagLabel' => $entity->getAuditForm()->getFlagLabel(),
                'totalScore' => $entity->getTotalScore(),
                'usedforms' => $uforms[$entity->getAuditReference()->getId()]
            );
            $result[] = $row;
        }
        return $result;
    }
}