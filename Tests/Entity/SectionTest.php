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
     * @covers CiscoSystems\AuditBundle\Entity\Section::getPosition
     */
    public function testPosition()
    {
        $form = new Form();
        $sections = array();
        $formSections = array();
        for( $i = 0; $i < 4; $i++ )
        {
            $section = new Section(
                'title for section ' . $i +1,
                'description for section ' . $i + 1
            );
            $sections[] = $section;
            $relation = new FormSection( $form, $section );
            $relation->setPosition( $i );
            $formSections[] = $relation;
        }
        $relations = new ArrayCollection( $formSections );
        $form->setSectionRelations( $relations );

        $section = new Section( 'new section', 'new description' );
        $relation = new FormSection( $form, $section );
        $form->addSectionRelation( $relation );
        $section->addFormRelation( $relation );
        $relation->setPosition( count( $form->getSections() ) );

        $this->assertEquals( 6, $section->getPosition( $form ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getForms
     */
    public function testForms()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $formSections[] = new FormSection( $form, $this->section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->section->setFormRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertEquals( count( $forms ), $this->section->getFormRelations()->count() );
        $this->assertEquals( count( $forms ), count( $this->section->getForms()) );
        $this->assertContains( $forms[count( $forms )-1], $this->section->getForms() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getForm
     */
    public function testForm()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $formSections[] = new FormSection( $form, $this->section );
        }
        $relations = new ArrayCollection( $formSections );
        $section = new Section( 'new section', 'new description' );
        $section->addForm( $forms[2] );
        $relations->add( new FormSection( $forms[2], $section ));
        $this->section->setFormRelations( $relations );

        $this->assertSame( $forms[2], $section->getForm() );
        $this->assertEquals( FALSE, $section->getFormRelation( $forms[2] )->getArchived() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::addForm
     */
    public function testAddForm()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $this->section->addForm( $form );
            $formSections[] = new FormSection( $form, $this->section );
            $this->assertEquals( 'title for form ' . $i, $forms[$i-1]->getTitle() );
        }
        $relations = new ArrayCollection( $formSections );

        $form = new Form( 'new form', 'this is a new form' );
        $this->section->addForm( $form );
        $forms[] = $form;
        $relation = new FormSection( $form, $this->section );
        $formSections[] = $relation;
        $relations->add( $relation );

        $this->assertSame(
            $relations->first()->getForm(),
            $this->section->getFormRelations()->first()->getForm()
        );
        $this->assertSame(
            $relations->last()->getForm(),
            $this->section->getFormRelations()->last()->getForm()
        );
        $this->assertEquals( $forms, $this->section->getForms() );
        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertFalse( $this->section->addForm( $form ) );
        $this->assertEquals( count( $forms ), $this->section->getFormRelations()->count() );
        $this->assertEquals( count( $forms ), count( $this->section->getForms()) );
        $this->assertContains( $form, $this->section->getForms() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::removeForm
     */
    public function testRemoveForm()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $this->section->addForm( $form );
            $formSections[] = new FormSection( $form, $this->section );
        }
        $relations = new ArrayCollection( $formSections );
        $lastForm = end( $forms );
        $this->section->removeForm( $lastForm );

        $this->assertSame(
            $relations->first()->getForm(),
            $this->section->getFormRelations()->first()->getForm()
        );
        $this->assertSame(
            $relations->last()->getForm(),
            $this->section->getFormRelations()->last()->getForm()
        );
        $this->assertEquals( $forms, $this->section->getForms() );
        $this->assertEquals( count( $relations ), count( $this->section->getFormRelations()) );
        $this->assertTrue( $this->section->getFormRelations()->last()->getArchived() );
        $this->assertTrue( $this->section->getFormRelation( $lastForm )->getArchived() );
        $this->assertEquals( 3, count( $forms ) );
        $this->assertEquals( 3, count( $this->section->getForms()) );
        $this->assertEquals( 3, count( $this->section->getFormRelations()) );
        $this->assertEquals( 2, count( $this->section->getForms( FALSE )) );
        $this->assertEquals( 1, count( $this->section->getForms( TRUE )) );
        $this->assertContains( $form, $this->section->getForms( TRUE ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getForms
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFormRelations
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFormRelations
     */
    public function testFormRelations()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $formSections[] = new FormSection( $form, $this->section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->section->setFormRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFormRelations() );
        $this->assertEquals( $forms, $this->section->getForms() );
        $this->assertEquals( count( $forms ), count( $formSections ) );
        $this->assertEquals( $relations->first()->getForm(), reset( $this->section->getForms()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFormRelation
     */
    public function testFormRelation()
    {
        $forms = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $form = new Form(
                'title for form ' . $i,
                'description for form ' . $i
            );
            $forms[] = $form;
            $formSections[] = new FormSection( $form, $this->section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->section->setFormRelations( $relations );

        $relation = $relations[2];
        $form = $relation->getForm();

        $this->assertEquals( count( $relations ), count( $this->section->getForms() ));
        $this->assertEquals( $forms, $this->section->getForms() );
        $this->assertEquals( $relation, $this->section->getFormRelation( $form ) );
        $this->assertEquals( $relations->first()->getForm(), reset( $this->section->getForms() ));
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
     */
    public function testFields()
    {
        $fields = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i,
                'description for field ' . $i
            );
            $fields[] = $field;
            $sectionFields[] = new SectionField( $this->section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->section->setFieldRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertEquals( count( $fields ), $this->section->getFieldRelations()->count() );
        $this->assertEquals( count( $fields ), count( $this->section->getFields() ));
        $this->assertContains( end( $fields ), $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::addField
     */
    public function testAddField()
    {
        $fields = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i,
                'description for field ' . $i
            );
            $fields[] = $field;
            $this->section->addField( $field );
            $sectionFields[] = new SectionField( $this->section, $field );
            $this->assertEquals( 'title for field ' . $i, end( $fields )->getTitle() );
        }
        $relations = new ArrayCollection( $sectionFields );

        $field = new Field( 'new field', 'this is a new field' );
        $this->section->addField( $field );
        $fields[] = $field;
        $relation = new SectionField( $this->section, $field );
        $sectionFields[] = $relation;
        $relations->add( $relation );

        $this->assertSame(
            $relations->first()->getField(),
            $this->section->getFieldRelations()->first()->getField()
        );
        $this->assertSame(
            $relations->last()->getField(),
            $this->section->getFieldRelations()->last()->getField()
        );
        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertFalse( $this->section->addField( $field ) );
        $this->assertEquals( count( $fields ), $this->section->getFieldRelations()->count() );
        $this->assertEquals( count( $fields ), count( $this->section->getFields() ));
        $this->assertContains( $field, $this->section->getFields() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::removeField
     */
    public function testRemoveField()
    {
        $fields = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i,
                'description for field ' . $i
            );
            $fields[] = $field;
            $this->section->addField( $field );
            $sectionFields[] = new SectionField( $this->section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $lastField = end( $fields );
        $this->section->removeField( $lastField );

        $this->assertSame(
            $relations->first()->getField(),
            $this->section->getFieldRelations()->first()->getField()
        );
        $this->assertSame(
            $relations->last()->getField(),
            $this->section->getFieldRelations()->last()->getField()
        );
        $this->assertEquals( count( $fields ), $this->section->getFieldRelations()->count() );
        $this->assertEquals( count( $fields ), count( $this->section->getFields() ));
        $this->assertTrue( $this->section->getFieldRelations()->last()->getArchived() );
        $this->assertTrue( $this->section->getFieldRelation( $lastField )->getArchived() );
        $this->assertEquals( 3, count( $fields ) );
        $this->assertEquals( 3, count( $this->section->getFields() ));
        $this->assertEquals( 2, count( $this->section->getFields( FALSE ) ));
        $this->assertEquals( 1, count( $this->section->getFields( TRUE ) ));
        $this->assertContains( $field, $this->section->getFields() );
        $this->assertContains( $field, $this->section->getFields( TRUE ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFields
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFieldRelations
     * @covers CiscoSystems\AuditBundle\Entity\Section::setFieldRelations
     */
    public function testFieldRelations()
    {
        $fields = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i,
                'description for field ' . $i
            );
            $fields[] = $field;
            $this->section->addField( $field );
            $sectionFields[] = new SectionField( $this->section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->section->setFieldRelations( $relations );

        $this->assertEquals( $relations, $this->section->getFieldRelations() );
        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertEquals( count( $fields ), count( $sectionFields ) );
        $this->assertEquals( $relations->first()->getField(), reset( $this->section->getFields()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Section::getFieldRelation
     */
    public function testFieldRelation()
    {
        $fields = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i,
                'description for field ' . $i
            );
            $fields[] = $field;
            $this->section->addField( $field );
            $sectionFields[] = new SectionField( $this->section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->section->setFieldRelations( $relations );

        $relation = $relations[2];
        $field = $relation->getField();

        $this->assertEquals( count( $relations ), count( $this->section->getFields() ));
        $this->assertEquals( $fields, $this->section->getFields() );
        $this->assertEquals( $relation, $this->section->getFieldRelation( $field ) );
        $this->assertEquals( $relations->first()->getField(), reset( $this->section->getFields() ));
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