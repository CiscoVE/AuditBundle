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

    public function testGetScoreForAudit()
    {
        $audit = $this->em
                      ->getRepository( 'CiscoSystemsAuditBundle:Audit' )
                       ->find( 3 );
        $this->assertEquals(
                $this->repo->qbScoresForAudit( $audit )->getDql(),
                'SELECT s ' .
                'FROM CiscoSystems\AuditBundle\Entity\Score s ' .
                'WHERE s.audit = :audit'

        );
        $this->assertNotEquals( 0, count( $this->repo->getScoresForAudit( $audit ) ));
    }
}