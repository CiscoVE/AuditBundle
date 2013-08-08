<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\SectionField;
use CiscoSystems\AuditBundle\Entity\Score;
use Doctrine\Common\Collections\ArrayCollection;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    protected $section;
    protected $field;

    protected function setUp()
    {
        parent::setUp();
        $this->field = new Field();
        $this->section = new Section();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\Field::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->field->setTitle( $title );

        $this->assertEquals( $title, $this->field->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\Field::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->field->setDescription( $description );

        $this->assertEquals( $description, $this->field->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setWeight
     * @covers CiscoSystems\AuditBundle\Entity\Field::getWeight
     */
    public function testWeight()
    {
        $weight = 5;
        $this->field->setWeight( $weight );

        $this->assertEquals( $weight, $this->field->getWeight() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\Field::getFlag
     */
    public function testFlag()
    {
        $flag = true;
        $this->field->setFlag( $flag );

        $this->assertEquals( $flag, $this->field->getFlag() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setChoices
     * @covers CiscoSystems\AuditBundle\Entity\Field::getChoices
     */
    public function testChoices()
    {
        $choices = array(
            'Y'     => 'correct answer',
            'N'     => 'incorrect answer',
            'A'     => 'acceptable answer',
            'N\A'   => 'non-applicable answer'
        );
        $this->field->setChoices( $choices );

        $this->assertEquals( $choices, $this->field->getChoices() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addChoice
     */
    public function testAddChoice()
    {
        $choices = array(
            'Y'     => 'correct answer',
            'N'     => 'incorrect answer',
            'A'     => 'acceptable answer',
            'N\A'   => 'non-applicable answer'
        );
        $mark = 'T';
        $label = 'test answer item';
        $this->field->setChoices( $choices );
        $this->field->addChoice( $mark, $label );

        $actualChoices = $this->field->getChoices();

        $this->assertEquals( $label, $actualChoices[$mark] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setScores
     * @covers CiscoSystems\AuditBundle\Entity\Field::getScores
     */
    public function testScores()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->field->setScores( $scores );

        $this->assertEquals( count( $scores ), count( $this->field->getScores()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addScore
     */
    public function testAddScore()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->field->setScores( $scores );

        $score = new Score();
        $this->field->addScore( $score );
        $actualScore = $this->field->getScores();

        $this->assertEquals( $score, $actualScore[( count( $actualScore ) -1 )] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeScore
     */
    public function testRemoveScore()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->field->setScores( $scores );

        $this->field->removeScore( $score3 );
        $scores->removeElement( $score3 );

        $this->assertNotContains( $score3, $this->field->getScores() );
        $this->assertEquals( $scores, $this->field->getScores() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getPosition
     */
    public function testPosition()
    {
        $section = new Section();
        $fields = array();
        $sectionFields = array();
        for( $i = 0; $i < 4; $i++ )
        {
            $field = new Field(
                'title for field ' . $i + 1,
                'description for field ' . $i + 1
            );
            $fields[] = $field;
            $relation = new SectionField( $section, $field );
            $relation->setPosition( $i );
            $sectionFields[] = $relation;
        }
        $relations = new ArrayCollection( $sectionFields );
        $section->setFieldRelations( $relations );

        $field = new Field( 'new field', 'new description' );
        $relation = new SectionField( $section, $field );
        $section->addFieldRelation( $relation );
        $field->addSectionRelation( $relation );
        $relation->setPosition( count( $section->getFields() ));

        $this->assertEquals( 6, $field->getPosition( $section ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSections
     */
    public function testSection()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->field->setSectionRelations( $relations );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertEquals( count( $sections ), $this->field->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $this->field->getSections()) );
        $this->assertContains( end( $sections ), $this->field->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addSection
     */
    public function testAddSection()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $this->field->addSection( $section );
            $sectionFields[] = new SectionField( $section, $this->field );
            $this->assertEquals( 'title for section ' . $i, $sections[$i-1]->getTitle() );
        }
        $relations = new ArrayCollection( $sectionFields );

        $section = new Section( 'new section', 'this is a new section' );
        $this->field->addSection( $section );
        $sections[] = $section;
        $relation = new SectionField( $section, $this->field );
        $sectionFields[] = $relation;
        $relations->add( $relation );

        $this->assertSame(
            $relations->first()->getSection(),
            $this->field->getSectionRelations()->first()->getSection()
        );
        $this->assertSame(
            $relations->last()->getSection(),
            $this->field->getSectionRelations()->last()->getSection()
        );
        $this->assertEquals( $sections, $this->field->getSections() );
        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertFalse( $this->field->addSection( $section ) );
        $this->assertEquals( count( $sections ), $this->field->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $this->field->getSections() ));
        $this->assertContains( $section, $this->field->getSections() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeSection
     */
    public function testRemoveSection()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $this->field->addSection( $section );
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $lastSection = end( $sections );
        $this->field->removeSection( $lastSection );

        $this->assertSame(
            $relations->first()->getSection(),
            $this->field->getSectionRelations()->first()->getSection()
        );
        $this->assertSame(
            $relations->last()->getSection(),
            $this->field->getSectionRelations()->last()->getSection()
        );
        $this->assertEquals( count( $sections ), count( $this->field->getSectionRelations()) );
        $this->assertEquals( count( $sections ), count( $this->field->getSections()) );
        $this->assertTrue( $this->field->getSectionRelations()->last()->getArchived() );
        $this->assertTrue( $this->field->getSectionRelation( $lastSection )->getArchived() );
        $this->assertEquals( 3, count( $sections ) );
        $this->assertEquals( 3, count( $this->field->getSections()) );
        $this->assertEquals( 2, count( $this->field->getSections( FALSE )) );
        $this->assertEquals( 1, count( $this->field->getSections( TRUE )) );
        $this->assertContains( $section, $this->field->getSections() );
        $this->assertContains( $section, $this->field->getSections( TRUE ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSections
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSectionRelations
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSectionRelations
     */
    public function testSectionRelations()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->field->setSectionRelations( $relations );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertEquals( $sections, $this->field->getSections() );
        $this->assertEquals( count( $sections ), count( $sectionFields ) );
        $this->assertEquals( $relations->first()->getSection(), reset( $this->field->getSections() ));
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSectionRelation
     */
    public function testSectionRelation()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->field->setSectionRelations( $relations );

        $relation = $relations[2];
        $section = $relation->getSection();

        $this->assertEquals( count( $relations ), count( $this->field->getSections() ));
        $this->assertEquals( $sections, $this->field->getSections() );
        $this->assertEquals( $relation, $this->field->getSectionRelation( $section ) );
        $this->assertEquals( $relations->first()->getSection(), reset( $this->field->getSections() ));
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addSectionRelation
     */
    public function testAddSectionRelation()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->field->setSectionRelations( $relations );

        $section = new Section( 'new Section', 'new Section added `a postoriori`' );
        $relation = new SectionField( $section, $this->field );
        $this->field->addSectionRelation( $relation );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertContains( $relation->getSection(), $this->field->getSections() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertFalse( $this->field->addSectionRelation( $relation ) );
        $this->assertContains( $relation, $this->field->getSectionRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeSectionRelation
     */
    public function testRemoveSectionRelation()
    {
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $this->field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $this->field->setSectionRelations( $relations );
        $relation3 = $relations->get( count( $relations ) - 1 );
        $this->field->removeSectionRelation( $relation3 );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertEquals( count( $relations ), count( $this->field->getSectionRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->field->getSectionRelations() );
        $this->assertNotContains( $relation3->getSection(), $this->field->getSections( FALSE ) );
        $this->assertContains( $relation3->getSection(), $this->field->getSections() );
    }
}
