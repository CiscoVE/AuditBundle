<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SectionRepositoryFunctionalTest extends WebTestCase
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
        $this->repo = $this->em->getRepository('CiscoSystemsAuditBundle:Section');
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

    public function testGetSections()
    {
        $form = $this->em
                     ->getRepository( 'CiscoSystemsAuditBundle:Form' )
                     ->find( 2 );
        $this->assertEquals(
            $this->repo->qbSections( $form )->getDql(),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Section s ' .
            'INNER JOIN CiscoSystemsAuditBundle:FormSection r ' .
            'WITH s = r.section ' .
            'INNER JOIN CiscoSystemsAuditBundle:Form f ' .
            'WITH f = r.form ' .
            'WHERE f = :form'
        );
        $this->assertNotEquals( 0, $this->repo->getSections( $form ) );
    }

}