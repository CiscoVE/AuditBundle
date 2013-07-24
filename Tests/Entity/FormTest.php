<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new Form();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\Form::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->form->setTitle( $title );

        $this->assertEquals( $title, $this->form->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\Form::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->form->setDescription( $description );

        $this->assertEquals( $description, $this->form->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setActive
     * @covers CiscoSystems\AuditBundle\Entity\Form::getActive
     */
    public function testActive()
    {
        $active = TRUE;
        $this->form->setActive( $active );

        $this->assertEquals( $active, $this->form->getActive() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setFlagLabel
     * @covers CiscoSystems\AuditBundle\Entity\Form::getFlagLabel
     */
    public function testFlagLabel()
    {
        $label = 'test label string';
        $this->form->setFlagLabel( $label );

        $this->assertEquals( $label, $this->form->getFlagLabel() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setSections
     * @covers CiscoSystems\AuditBundle\Entity\Form::getSections
     */
    public function testSections()
    {
        $section1 = new Section();
        $section2 = new Section();
        $section3 = new Section();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $this->assertEquals( count( $sections ), count( $this->form->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::addSection
     */
    public function testAddSection()
    {
        $section1 = new Section();
        $section2 = new Section();
        $section3 = new Section();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $section = new Section();
        $this->form->addSection( $section );

        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertContains( $section, $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::removeSection
     */
    public function testRemoveSection()
    {
        $section1 = new Section();
        $section2 = new Section();
        $section3 = new Section();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $this->form->removeSection( $section3 );
        $sections->removeElement( $section3 );

        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertNotContains( $section3, $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setAudits
     * @covers CiscoSystems\AuditBundle\Entity\Form::getAudits
     */
    public function testAudits()
    {
        $audit1 = new Audit();
        $audit2 = new Audit();
        $audit3 = new Audit();
        $audits = new ArrayCollection( array( $audit1, $audit2, $audit3 ));
        $this->form->setAudits( $audits );

        $this->assertEquals( $audits, $this->form->getAudits() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::addAudit
     */
    public function testAddAudit()
    {
        $audit1 = new Audit();
        $audit2 = new Audit();
        $audit3 = new Audit();
        $audits = new ArrayCollection( array( $audit1, $audit2, $audit3 ));
        $this->form->setAudits( $audits );

        $audit = new Audit();
        $this->form->addAudit( $audit );

        $this->assertEquals( $audits, $this->form->getAudits() );
        $this->assertContains( $audit, $this->form->getAudits() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::removeAudit
     */
    public function testRemoveAudit()
    {
        $audit1 = new Audit();
        $audit2 = new Audit();
        $audit3 = new Audit();
        $audits = new ArrayCollection( array( $audit1, $audit2, $audit3 ));
        $this->form->setAudits( $audits );

        $this->form->removeAudit( $audit3 );
        $audits->removeElement( $audit3 );

        $this->assertEquals( $audits, $this->form->getAudits() );
        $this->assertNotContains( $audit3, $this->form->getAudits() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::setMetadata
     * @covers CiscoSystems\AuditBundle\Entity\Form::getMetadata
     */
    public function testMetadata()
    {
        // not implemented as Metadata is an interface
    }
}