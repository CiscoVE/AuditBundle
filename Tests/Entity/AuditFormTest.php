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

        $actual = $this->form->getTitle();
        $expected = $title;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->form->setDescription( $description );

        $actual = $this->form->getDescription();
        $expected = $description;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setActive
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getActive
     */
    public function testActive()
    {
        $active = TRUE;
        $this->form->setActive( $active );

        $actual = $this->form->getActive();
        $expected = $active;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::setFlagLabel
     * @covers CiscoSystems\AuditBundle\Entity\AuditForm::getFlagLabel
     */
    public function testFlagLabel()
    {
        $label = 'test label string';
        $this->form->setFlagLabel( $label );

        $actual = $this->form->getFlagLabel();
        $expected = $label;

        $this->assertEquals( $expected, $actual );
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

        $actual = count( $this->form->getSections());
        $expected = count( $sections );

        $this->assertEquals( $expected, $actual );
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

        $actualSections = $this->form->getSections();
        $actual = $actualSections[( count( $actualSections ) - 1 )];
        $expected = $section;

        $this->assertEquals( $expected, $actual );
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

        $actual = $this->form->getSections();
        $expected = $section3;

        $this->assertNotContains( $expected, $actual );
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

        $actual = $this->form->getAudits();
        $expected = $audits;

        $this->assertEquals( $expected, $actual );
    }
}