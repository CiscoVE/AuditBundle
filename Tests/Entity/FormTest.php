<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\FormSection;
use CiscoSystems\AuditBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

class FormTest extends \PHPUnit_Framework_TestCase
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

    public function testSection()
    {
        $sections = array();
        $this->formSections = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $section = new Section();
            $section->setTitle( 'title for section ' . $i )
                    ->setDescription( 'decription for section ' . $i );
            $sections[] = $section;
            $this->formSections[] = new FormSection( $this->form, $section );
        }
        $relations = new ArrayCollection( $this->formSections );
        $this->form->setSectionRelations( $relations );

        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertEquals( count( $sections ), $this->form->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $this->form->getSections()) );
        $this->assertContains( $sections[count( $section )-1], $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::getAddSection
     */
    public function testAddSection()
    {
        $sections = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $this->form->addSection( $section );
            $formSections[] = new FormSection( $this->form, $section );
            $this->assertEquals( 'title for section ' . $i, $sections[$i-1]->getTitle() );
        }
        $relations = new ArrayCollection( $formSections );

        $section = new Section( 'new section', 'this is a new section' );
        $this->form->addSection( $section );
        $sections[] = $section;
        $relation = new FormSection( $this->form, $section );
        $formSections[] = $relation;
        $relations->add( $relation );

        $this->assertEquals(
            $relations->first()->getSection()->getTitle(),
            $this->form->getSectionRelations()->first()->getSection()->getTitle()
        );
        $this->assertEquals(
            $relations->last()->getSection()->getTitle(),
            $this->form->getSectionRelations()->last()->getSection()->getTitle()
        );
        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertFalse( $this->form->addSection( $section ) );
        $this->assertEquals( count( $sections ), $this->form->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $this->form->getSections()) );
        $this->assertContains( $section, $this->form->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::getSections
     * @covers CiscoSystems\AuditBundle\Entity\Form::getSectionRelations
     * @covers CiscoSystems\AuditBundle\Entity\Form::setSectionRelations
     */
    public function testSectionRelations()
    {
        $sections = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $formSections[] = new FormSection( $this->form, $section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->form->setSectionRelations( $relations );

        $relation = $relations[2];
        $section = $relation->getSection();

        $this->assertEquals( count( $relations ), count( $this->form->getSections()) );
        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertEquals( $relation, $this->form->getSectionRelation( $section ) );
        $this->assertEquals( $relations->first()->getSection(), reset( $this->form->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::addSectionRelations
     */
    public function testAddSectionRelation()
    {
        $sections = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $formSections[] = new FormSection( $this->form, $section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->form->setSectionRelations( $relations );

        $section = new Section( 'new Section', 'new Section added `a postoriori`' );
        $relation = new FormSection( $this->form, $section );
        $this->form->addSectionRelation( $relation );

        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertContains( $relation->getSection(), $this->form->getSections() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertFalse( $this->form->addSectionRelation( $relation ) );
        $this->assertContains( $relation, $this->form->getSectionRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::removeSectionRelations
     */
    public function testRemoveSectionRelation()
    {
        $sections = array();
        $formSections = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $formSections[] = new FormSection( $this->form, $section );
        }
        $relations = new ArrayCollection( $formSections );
        $this->form->setSectionRelations( $relations );
        $relation3 = $relations->get( count( $relations ) - 1 );
        $this->form->removeSectionRelation( $relation3 );

        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertEquals( count( $relations ), count( $this->form->getSectionRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->form->getSectionRelations() );
        $this->assertNotContains( $relation3->getSection(), $this->form->getSections( FALSE ) );
        $this->assertContains( $relation3->getSection(), $this->form->getSections() );
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