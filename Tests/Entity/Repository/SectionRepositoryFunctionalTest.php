<?php

namespace CiscoSystems\AuditBundle\Tests\Entity\Repository;

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
        $archived = FALSE;
        $this->assertEquals(
            $this->repo->qbSections( $form, $archived )->getDql(),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Section s ' .
            'INNER JOIN CiscoSystemsAuditBundle:FormSection r ' .
            'WITH s = r.section ' .
            'INNER JOIN CiscoSystemsAuditBundle:Form f ' .
            'WITH f = r.form ' .
            'WHERE f = :form ' .
            'AND r.archived = :archived'
        );
        $this->assertNotEquals( 0, $this->repo->getSections( $form ) );
    }

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\SectionRepository::qbAttached
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\SectionRepository::qbDetached
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\SectionRepository::getDetachedSections
     */
    public function testGetDetachedSections()
    {
        $sections = $this->repo
                         ->findAll();
        $this->assertEquals(
            $this->repo->qbAttached()->getDql(),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Section s ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\FormSection r ' .
            'WITH r.section = s ' .
            'GROUP BY r.section'
        );
        $this->assertEquals(
            $this->repo->qbDetached( $sections )->getDql(),
            'SELECT s ' .
            'FROM CiscoSystems\AuditBundle\Entity\Section s ' .
            'WHERE s NOT IN ( :sections )'
        );
        $all = count( $sections );
        $attached = count( $this->repo->qbAttached()->getQuery()->getResult() );
        $detached = count( $this->repo->getDetachedSections() );

        $this->assertEquals( $detached, $all - $attached );
        $this->assertNotEquals( 0, $this->repo->getDetachedSections() );
    }

}