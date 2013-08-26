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

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\AuditRepository::qbAudits
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\AuditRepository::getAuditsPerAuditor
     */
    public function testGetAudits()
    {
        $this->assertEquals(
                $this->repo->qbAudits()->getDql(),
                'SELECT a ' .
                'FROM CiscoSystems\AuditBundle\Entity\Audit a'
        );
        $this->assertNotEquals( 0, count( $this->repo->getAuditsPerAuditor() ));
        $this->assertNotEquals( 0, count( $this->repo->getAuditsWithFormsUsage() ));
    }

    /**
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\AuditRepository::qbReferences
     * @covers \CiscoSystems\AuditBundle\Entity\Repository\AuditRepository::getCaseId
     */
    public function testGetReference()
    {
        $this->assertEquals(
                $this->repo->qbReferences()->getDql(),
                'SELECT IDENTITY( c.reference ) ' .
                'FROM CiscoSystems\AuditBundle\Entity\Audit c ' .
                'ORDER BY c.reference DESC'
        );
        $this->assertNotEquals( 0, count( $this->repo->getCaseId() ));
    }

    public function testGetAuditByFormAndReference()
    {
        // not implemented correctly as the method is ment to retrieve audit
        // with reference and form
//        $form = new \CiscoSystems\AuditBundle\Entity\Form();
//        $refId = 25560;
//        $this->assertEquals(
//                $this->repo->qbAuditByFormAndReference( $form, $refId )->getDql(),
//                'SELECT a ' .
//                'FROM CiscoSystems\AuditBundle\Entity\Audit a ' .
//                'INNER JOIN CiscoSystems\AuditBundle\Model\ReferenceInterface r ' .
//                'WITH a.reference = r ' .
//                'WHERE a.form = :form ' .
//                'AND r.id = :refid'
//        );
//        $this->assertNotEquals( 0, count( $this->repo->getAuditByFormAndReference( $form, $refId ) ));
    }

    public function testGetFormState()
    {
        $id = 8; $index = array(
            'forms'     => array( 8 ),
            'sections'  => array( 13, 14 ),
            'fields'    => array( 30, 31, 32, 33, 34 )
        );

        $this->assertEquals(
            $this->repo->qbFormState( $index )->getDql(),
            'SELECT a ' .
            'FROM CiscoSystems\AuditBundle\Entity\Audit a ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\Form fo WITH fo = a.form ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\FormSection fs WITH fo = fs.form ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\Section s WITH s = fs.section ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\SectionField sf WITH s = sf.section ' .
            'INNER JOIN CiscoSystems\AuditBundle\Entity\Field fi WITH fi = sf.field ' .
            'WHERE a.id = :id ' .
            'AND s.id IN ( :sections ) ' .
            'AND fi.id IN ( :fields )'
        );

//        $form = $this->repo->getFormState( $id );

        $this->assertNotEquals( 0, count( $this->repo->getFormState( $id ) ));
    }
}