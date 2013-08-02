<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\FormSection;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\SectionField;
use CiscoSystems\AuditBundle\Entity\Field;
use Doctrine\Common\Collections\ArrayCollection;

class SectionTest extends \PHPUnit_Framework_TestCase
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
     * @covers CiscoSystems\AuditBundle\Entity\Section::getForms
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFormRelations
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFormRelations
     */
    public function testFormRelations()
    {
        $relation1 = new FormSection( new Form(), $this->section );
        $relation2 = new FormSection( new Form(), $this->section );
        $relation3 = new FormSection( new Form(), $this->section );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFormRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertEquals( $relations->first()->getForm(), reset( $this->section->getForms()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::addFormRelation
     */
    public function testAddFormRelation()
    {
        $relation1 = new FormSection( new Form(), $this->section );
        $relation2 = new FormSection( new Form(), $this->section );
        $relation3 = new FormSection( new Form(), $this->section );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFormRelations( $relations );

        $relation = new FormSection( new Form(), $this->section );
        $this->section->addFormRelation( $relation );

        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertContains( $relation->getForm(), $this->section->getForms() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertContains( $relation, $this->section->getFormRelations() );
    }

    public function testRemoveFormRelation()
    {
        $relation1 = new FormSection( new Form(), $this->section );
        $relation2 = new FormSection( new Form(), $this->section );
        $relation3 = new FormSection( new Form(), $this->section );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFormRelations( $relations );
        $this->section->removeFormRelation( $relation3 );

        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertEquals( count( $relations ), count( $this->section->getFormRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->section->getFormRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFields
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFieldRelations
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFieldRelations
     */
    public function testFieldRelations()
    {
        $relation1 = new SectionField( $this->section, new Field() );
        $relation2 = new SectionField( $this->section, new Field() );
        $relation3 = new SectionField( $this->section, new Field() );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFieldRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertEquals( $relations->first()->getField(), reset( $this->section->getFields()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::addFieldRelation
     */
    public function testAddFieldRelation()
    {
        $relation1 = new SectionField( $this->section, new Field() );
        $relation2 = new SectionField( $this->section, new Field() );
        $relation3 = new SectionField( $this->section, new Field() );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFieldRelations( $relations );

        $relation = new SectionField( $this->section, new Field() );
        $this->section->addFieldRelation( $relation );

        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertContains( $relation, $this->section->getFieldRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::removeFieldRelation
     */
    public function testRemoveFieldRelation()
    {
        $relation1 = new SectionField( $this->section, new Field() );
        $relation2 = new SectionField( $this->section, new Field() );
        $relation3 = new SectionField( $this->section, new Field() );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFieldRelations( $relations );
        $this->section->removeFieldRelation( $relation3 );

        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->section->getFieldRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getWeight
     */
    public function testGetWeight()
    {
        $weight = 5;
        $field1 = new Field();
        $relation1 = new SectionField();
        $relation1->setField( $field1 );
        $field1->setWeight( $weight );
        $field2 = new Field();
        $relation2 = new SectionField();
        $relation2->setField( $field2 );
        $field2->setWeight( $weight );
        $field3 = new Field();
        $relation3 = new SectionField();
        $relation3->setField( $field3 );
        $field3->setWeight( $weight );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->section->setFieldRelations( $relations );

        $this->assertEquals( ( $weight * 3 ), $this->section->getWeight() );
    }
}