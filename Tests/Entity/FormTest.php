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

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::getSections
     * @covers CiscoSystems\AuditBundle\Entity\Form::getSectionRelations
     * @covers CiscoSystems\AuditBundle\Entity\Form::setSectionRelations
     */
    public function testSectionRelations()
    {
        $section1 = new Section();
        $section2 = new Section();
        $section3 = new Section();
        $relation1 = new FormSection( $this->form, $section1 );
        $relation2 = new FormSection( $this->form, $section2 );
        $relation3 = new FormSection( $this->form, $section3 );

        $sections = array( $section1, $section2, $section3 );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->form->setSectionRelations( $relations );

        $this->assertEquals( count( $relations ), count( $this->form->getSections()) );
        $this->assertEquals( $sections, $this->form->getSections() );
        $this->assertEquals( $relations->first()->getSection(), reset( $this->form->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::addSectionRelations
     */
    public function testAddSectionRelation()
    {
        $relation1 = new FormSection( $this->form, new Section() );
        $relation2 = new FormSection( $this->form, new Section() );
        $relation3 = new FormSection( $this->form, new Section() );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->form->setSectionRelations( $relations );

        $relation = new FormSection( $this->form, new Section() );
        $this->form->addSectionRelation( $relation );

        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertContains( $relation->getSection(), $this->form->getSections() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertContains( $relation, $this->form->getSectionRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Form::removeSectionRelations
     */
    public function testRemoveSectionRelation()
    {
        $relation1 = new FormSection( $this->form, new Section() );
        $relation2 = new FormSection( $this->form, new Section() );
        $relation3 = new FormSection( $this->form, new Section() );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->form->setSectionRelations( $relations );
        $this->form->removeSectionRelation( $relation3 );

        $this->assertEquals( $relations, $this->form->getSectionRelations() );
        $this->assertEquals( count( $relations ), count( $this->form->getSectionRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->form->getSectionRelations() );
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