<?php

namespace CiscoSystems\AuditBundle\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormRepositoryFunctionalTest extends WebTestCase
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
        $this->repo = $this->em->getRepository('CiscoSystemsAuditBundle:Form');
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

    public function testGetForms()
    {
        $section = $this->em
                        ->getRepository( 'CiscoSystemsAuditBundle:Section' )
                        ->find( 6 );
        $archived = FALSE;
        $this->assertEquals(
                $this->repo->qbForms( $section, $archived )->getDql(),
                'SELECT f ' .
                'FROM CiscoSystems\AuditBundle\Entity\Form f ' .
                'JOIN CiscoSystems\AuditBundle\Entity\FormSection r ' .
                'WITH f = r.form ' .
                'JOIN CiscoSystems\AuditBundle\Entity\Section s ' .
                'WITH r.section = s ' .
                'WHERE s = :section ' .
                'AND r.archived = :archived'
        );
        $this->assertNotEquals( 0, count( $this->repo->getForms( $section, $archived ) ));
    }
}