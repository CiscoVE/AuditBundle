<?php
namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\SectionField;
use CiscoSystems\AuditBundle\Entity\Section;

class SectionFieldTest extends \PHPUnit_Framework_TestCase
{
    protected $section;
    protected $field;
    protected $relation;

    protected function setUp()
    {
        parent::setUp();
        $this->field = new Field();
        $this->section = new Section();
        $this->relation = new SectionField();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\Field::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->relation->setPosition( $position );

        $this->assertEquals( $position + 1, $this->relation->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSection
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSection
     */
    public function testSection()
    {
        $section = $this->section;
        $this->relation->setSection( $section );

        $this->assertEquals( $section, $this->relation->getSection() );
    }
}