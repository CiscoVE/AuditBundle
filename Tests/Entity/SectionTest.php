<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Field;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormSectionTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $section;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new Form();
        $this->section = new Section();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\Section::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->section->setTitle( $title );

        $this->assertEquals( $title, $this->section->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\Section::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->section->setDescription( $description );

        $this->assertEquals( $description, $this->section->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\Section::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->section->setPosition( $position );

        $this->assertEquals( $position, $this->section->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFlag
     */
    public function testFlag()
    {
        $flag = true;
        $this->section->setFlag( $flag );

        $this->assertEquals( $flag, $this->section->getFlag() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setForm
     * @covers CiscoSystems\AuditBundle\Entity\Section::getForm
     */
    public function testAuditForm()
    {
        $form = $this->form;
        $this->section->setForm( $form );

        $this->assertEquals( $form, $this->section->getForm() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFields
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFields
     */
    public function testFields()
    {
        $field1 = new Field();
        $field2 = new Field();
        $field3 = new Field();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $this->assertEquals( $fields, $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::addField
     */
    public function testAddField()
    {
        $field1 = new Field();
        $field2 = new Field();
        $field3 = new Field();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $field = new Field();
        $this->section->addField( $field );
        $actualFields = $this->section->getFields();

        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertContains( $field, $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::removeField
     */
    public function testRemoveField()
    {
        $field1 = new Field();
        $field2 = new Field();
        $field3 = new Field();
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $this->section->removeField( $field3 );
        $fields->removeElement( $field3 );

        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertNotContains( $field3, $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getWeight
     */
    public function testGetWeight()
    {
        $weight = 5;
        $field1 = new Field();
        $field1->setWeight( $weight );
        $field2 = new Field();
        $field2->setWeight( $weight );
        $field3 = new Field();
        $field3->setWeight( $weight );
        $fields = new ArrayCollection( array( $field1, $field2, $field3 ));
        $this->section->setFields( $fields );

        $this->assertEquals( ( $weight * 3 ), $this->section->getWeight() );
    }
}