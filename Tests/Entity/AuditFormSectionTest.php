<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\AuditForm;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormSectionTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $section;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new AuditForm();
        $this->section = new AuditFormSection();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->section->setTitle( $title );

        $actual = $this->section->getTitle();
        $expected = $title;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->section->setDescription( $description );

        $actual = $this->section->getDescription();
        $expected = $description;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->section->setPosition( $position );

        $actual = $this->section->getPosition();
        $expected = $position;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getFlag
     */
    public function testFlag()
    {
        $flag = true;
        $this->section->setFlag( $flag );

        $actual = $this->section->getFlag();
        $expected = $flag;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setAuditForm
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getAuditForm
     */
    public function testAuditForm()
    {
        $form = $this->form;
        $this->section->setAuditForm( $form );

        $actual = $this->section->getAuditForm();
        $expected = $form;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::addField
     */
    public function testAddField()
    {
        $field1 = new AuditFormField();
        $field2 = new AuditFormField();
        $field3 = new AuditFormField();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $field = new AuditFormField();
        $this->section->addField( $field );

        $actualFields = $this->section->getFields();
        $actual = $actualFields[( count( $actualFields ) -1 )];
        $expected = $field;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::removeField
     */
    public function testRemoveField()
    {
        $field1 = new AuditFormField();
        $field2 = new AuditFormField();
        $field3 = new AuditFormField();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));

        $this->section->setFields( $fields );
        $this->section->removeField( $field3 );
        $fields->removeElement( $field3 );

        $actual = count( $this->section->getFields() );
        $expected = count( $fields );

        $this->assertEquals( $expected, $actual );
    }
}