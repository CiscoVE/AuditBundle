<?php
namespace CiscoSystems\AuditBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ScoreRepository extends EntityRepository
{
    public function qbScoreForAudit( $audit )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.audit = :audit' )
                    ->setParameter( 'audit', $audit );
    }

    public function getScoreForAudit( $audit )
    {
        return $this->qbScoreForAudit( $audit )
                    ->getQuery()
                    ->getResult();
    }

    public function qbScoreForField( $field )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.field = :field' )
                    ->setParameter( 'field', $field );
    }

    public function getScoreForField( $field )
    {
        return $this->qbScoreForField( $field )
                    ->getQuery()
                    ->getResult();
    }

    public function qbScoreForAuditAndField( $audit, $field )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.audit = :audit' )
                    ->andWhere( 's.field = :field ')
                    ->setParameters( array(
                        'audit' => $audit,
                        'field' => $field
                    ));
    }

    public function getScoreForAuditAndField( $audit, $field )
    {
        return $this->qbScoreForAuditAndField( $audit, $field )
                    ->getQuery()
                    ->getResult();
    }
}