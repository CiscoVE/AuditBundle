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

        $this->assertEquals( $title, $this->section->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->section->setDescription( $description );

        $this->assertEquals( $description, $this->section->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->section->setPosition( $position );

        $this->assertEquals( $position, $this->section->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getFlag
     */
    public function testFlag()
    {
        $flag = true;
        $this->section->setFlag( $flag );

        $this->assertEquals( $flag, $this->section->getFlag() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setAuditForm
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getAuditForm
     */
    public function testAuditForm()
    {
        $form = $this->form;
        $this->section->setAuditForm( $form );

        $this->assertEquals( $form, $this->section->getAuditForm() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::setFields
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getFields
     */
    public function testFields()
    {
        $field1 = new AuditFormField();
        $field2 = new AuditFormField();
        $field3 = new AuditFormField();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $this->assertEquals( $fields, $this->section->getFields() );
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

        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertContains( $field, $this->section->getFields() );
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

        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertNotContains( $field3, $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormSection::getWeight
     */
    public function testGetWeight()
    {
        $weight = 5;
        $field1 = new AuditFormField();
        $field1->setWeight( $weight );
        $field2 = new AuditFormField();
        $field2->setWeight( $weight );
        $field3 = new AuditFormField();
        $field3->setWeight( $weight );
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $this->assertEquals( ( $weight * 3 ), $this->section->getWeight() );
    }
}