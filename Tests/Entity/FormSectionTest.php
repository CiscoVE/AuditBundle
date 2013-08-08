<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\FormSection;
use CiscoSystems\AuditBundle\Entity\Section;

class FormSectionTest extends \PHPUnit_Framework_TestCase
{
    protected $section;
    protected $form;
    protected $relation;

    protected function setUp()
    {
        parent::setUp();
        $this->form = new Form();
        $this->section = new Section();
        $this->relation = new FormSection();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::getArchived
     */
    public function testArchived()
    {
        $archived = TRUE;
        $this->relation->setArchived( $archived );

        $this->assertTrue( $this->relation->getArchived() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->relation->setPosition( $position );

        $this->assertEquals( $position + 1, $this->relation->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::setForm
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::getForm
     */
    public function testForm()
    {
        $form = $this->form;
        $this->relation->setForm( $form );

        $this->assertEquals( $form, $this->relation->getForm() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::setSection
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::getSection
     */
    public function testSection()
    {
        $section = $this->section;
        $this->relation->setSection( $section );

        $this->assertEquals( $section, $this->relation->getSection() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\FormSection::getType
     */
    public function testType()
    {
        $this->assertEquals(
            \CiscoSystems\AuditBundle\Entity\Relation::TYPE_FORMSECTION,
            $this->relation->getType()
        );
    }
}