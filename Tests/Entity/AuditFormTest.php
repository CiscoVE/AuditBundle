<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\AuditForm;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new AuditForm();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->form->setTitle( $title );

        $this->assertEquals( $title, $this->form->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->form->setDescription( $description );

        $this->assertEquals( $description, $this->form->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setActive
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getActive
     */
    public function testActive()
    {
        $active = TRUE;
        $this->form->setActive( $active );

        $this->assertEquals( $active, $this->form->getActive() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setFlagLabel
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getFlagLabel
     */
    public function testFlagLabel()
    {
        $label = 'test label string';
        $this->form->setFlagLabel( $label );

        $this->assertEquals( $label, $this->form->getFlagLabel() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setSections
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getSections
     */
    public function testSections()
    {
        $section1 = new AuditFormSection();
        $section2 = new AuditFormSection();
        $section3 = new AuditFormSection();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $this->assertEquals( count( $sections ), count( $this->form->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::addSection
     */
    public function testAddSection()
    {
        $section1 = new AuditFormSection();
        $section2 = new AuditFormSection();
        $section3 = new AuditFormSection();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $section = new AuditFormSection();
        $this->form->addSection( $section );

        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertContains( $section, $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::removeSection
     */
    public function testRemoveSection()
    {
        $section1 = new AuditFormSection();
        $section2 = new AuditFormSection();
        $section3 = new AuditFormSection();
        $sections = new ArrayCollection( array( $section1, $section2, $section3 ));
        $this->form->setSections( $sections );

        $this->form->removeSection( $section3 );
        $sections->removeElement( $section3 );

        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertNotContains( $section3, $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setAudits
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getAudits
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
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::addAudit
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
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::removeAudit
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
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setMetadata
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getMetadata
     */
    public function testMetadata()
    {
        // not implemented as Metadata is an interface
    }
}