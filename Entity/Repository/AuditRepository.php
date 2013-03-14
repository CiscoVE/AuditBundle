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

    public function getAuditGroupByForms()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select( 'a' )->from( 'CiscoSystemsAuditBundle:Audit', 'a' );
        $entities = $qb->getQuery()->getResult();

        $forms = array();
        $result = array();

        foreach ( $entities as $entity )
        {
            $id = $entity->getAuditReference()->getId();
            if( !array_key_exists( $id, $forms ) )
            {
                $forms[$id] = array( $entity->getAuditForm() );
            }
            else
            {
                if( !in_array( $entity->getAuditForm(), $forms ))
                {
                    array_push($forms[$id], $entity->getAuditForm());
                }
            }
        }

        foreach ( $entities as $entity )
        {
            $row = array(
                'id' => $entity->getId(),
                'auditReference' => $entity->getAuditReference(),
                'auditForm' => $entity->getAuditForm(),
                'auditingUser' => $entity->getAuditingUser(),
                'flag' => $entity->getFlag(),
                'totalScore' => $entity->getTotalScore(),
                'usedforms' => $forms[$entity->getAuditReference()->getId()]
            );
            $result[] = $row;
        }
        return $result;
    }
}