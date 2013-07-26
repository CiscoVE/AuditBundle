<?php

namespace CiscoSystems\AuditBundle\Tests\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuditRepositoryTest extends WebTestCase
{
    private $repo;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->repo = $kernel->getContainer()
                             ->get('doctrine.orm.entity_manager')
                             ->getRepository('CiscoSystemsAuditBundle:Audit');
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

    public function testGetAudits()
    {
        $this->assertEquals(
                $this->repo->qbAudits()->getDql(),
                'SELECT a ' .
                'FROM CiscoSystems\AuditBundle\Entity\Audit a'

        );
        $this->assertNotEquals( 0, count( $this->repo->getAuditsPerAuditor() ));
    }

    public function testGetReference()
    {
        $this->assertEquals(
                $this->repo->qbReferences()->getDql(),
                'SELECT c.reference ' .
                'FROM CiscoSystems\AuditBundle\Entity\Audit c ' .
                'ORDER BY c.reference DESC'
        );
    }
}