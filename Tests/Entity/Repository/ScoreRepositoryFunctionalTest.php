<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use CiscoSystems\AuditBundle\Entity\Audit;

class ScoreRepositoryFunctionalTest extends WebTestCase
{
    private $em;
    private $repo;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()
                           ->get('doctrine.orm.entity_manager');
        $this->repo = $this->em->getRepository('CiscoSystemsAuditBundle:Score');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::qbScoresForAudit
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::getScoresForAudit
     */
    public function testGetScoresForAudit()
    {
        $audit = $this->em
                      ->getRepository( 'CiscoSystemsAuditBundle:Audit' )
                      ->find( 6 );
        $this->assertEquals(
                $this->repo->qbScoresForAudit( $audit )->getDql(),
                'SELECT s ' .
                'FROM CiscoSystems\AuditBundle\Entity\Score s ' .
                'WHERE s.audit = :audit'

        );
        $this->assertNotEquals( 0, count( $this->repo->getScoresForAudit( $audit ) ));
    }

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::qbScoresForField
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::getScoresForField
     */
    public function testGetScoresForField()
    {
        $field = $this->em
                      ->getRepository( 'CiscoSystemsAuditBundle:Field' )
                      ->find( 18 );
        $this->assertEquals(
            $this->repo->qbScoresForField( $field )->getDql(),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Score s ' .
            'WHERE s.field = :field'
        );
        $this->assertNotEquals( 0, count( $this->repo->getScoresForField( $field ) ));
    }

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::qbScoreForAuditAndField
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository::getScoreForAuditAndField
     */
    public function testGetScoreForAuditAndField()
    {
        $audit = $this->em
                      ->getRepository( 'CiscoSystemsAuditBundle:Audit' )
                      ->find( 6 );
        $field = $this->em
                      ->getRepository( 'CiscoSystemsAuditBundle:Field' )
                      ->find( 18 );
        $this->assertEquals(
            $this->repo->qbScoreForAuditAndField( $audit, $field ),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Score s ' .
            'WHERE s.audit = :audit ' .
            'AND s.field = :field'
        );
        $this->assertNotEquals( 0, count( $this->repo->getScoreForAuditAndField( $audit, $field ) ));
    }
}