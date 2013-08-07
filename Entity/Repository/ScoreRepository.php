<?php
namespace CiscoSystems\AuditBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ScoreRepository extends EntityRepository
{
    public function qbScoresForAudit( $audit )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.audit = :audit' )
                    ->setParameter( 'audit', $audit );
    }

    public function getScoresForAudit( $audit )
    {
        return $this->qbScoresForAudit( $audit )
                    ->getQuery()
                    ->getResult();
    }

    public function qbScoresForField( $field )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.field = :field' )
                    ->setParameter( 'field', $field );
    }

    public function getScoresForField( $field )
    {
        return $this->qbScoresForField( $field )
                    ->getQuery()
                    ->getResult();
    }

    public function qbScoreForAuditAndField( $audit, $field )
    {
        return $this->createQueryBuilder( 's' )
                    ->where( 's.audit = :audit' )
                    ->andWhere( 's.field = :field')
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