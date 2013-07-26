<?php

namespace CiscoSystems\AuditBundle\Tests\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuditRepositoryFunctionalTest extends WebTestCase
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
        $this->repo = $this->em->getRepository('CiscoSystemsAuditBundle:Audit');
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
                'SELECT IDENTITY( c.reference ) ' .
                'FROM CiscoSystems\AuditBundle\Entity\Audit c ' .
                'ORDER BY c.reference DESC'
        );
    }

//    public function testGetAuditByFormAndReference()
//    {
//        $form = new \CiscoSystems\AuditBundle\Entity\Form();
//        $refId = 25560;
//        $this->assertEquals(
//                $this->repo->qbAuditByFormAndReference( $form, $refId ),
//                'SELECT a ' .
//                'FROM CiscoSystemsAuditBundle:Audit a ' .
//                'WHERE a.reference.id = :refid ' .
//                'AND a.form = :form'
//        );
//    }
}