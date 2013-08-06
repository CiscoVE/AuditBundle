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

    private function resetField()
    {
        $this->field = new Field();
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
     * @covers CiscoSystems\AuditBundle\Entity\Field::setWeigth
     * @covers CiscoSystems\AuditBundle\Entity\Field::GetWeigth
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
     * @covers CiscoSystems\AuditBundle\Entity\Field::addAuditScore
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
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeAuditScore
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

    public function testSection()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $field->setSectionRelations( $relations );

        $this->assertEquals( 3, count( $sections ) );
        $this->assertEquals( 3, count( $relations ) );
        $this->assertEquals( 3, $field->getSectionRelations()->count() );

        $this->assertEquals( $relations, $field->getSectionRelations() );
        $this->assertEquals( count( $sections ), $field->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $field->getSections()) );
        $this->assertContains( $sections[count( $sections )-1], $field->getSections() );
    }

    public function testAddSection()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $field->addSection( $section );
            $sectionFields[] = new SectionField( $section, $field );
            $this->assertEquals( 'title for section ' . $i, $sections[$i-1]->getTitle() );
        }
        $relations = new ArrayCollection( $sectionFields );

        $section = new Section( 'new section', 'this is a new section' );
        $field->addSection( $section );
        $sections[] = $section;
        $sectionFields[] = new SectionField( $section, $field );
        $relations->add( new SectionField( $section, $field ));

        $this->assertEquals( 'title for section 1', $sections[0]->getTitle() );
//        $this->assertEquals( 4, count( $sections ) );
//        $this->assertEquals( 4, count( $relations ) );
//        $this->assertEquals( 4, $field->getSectionRelations()->count() );
        $this->assertEquals(
            $relations->last()->getSection()->getTitle(),
            $field->getSectionRelations()->last()->getSection()->getTitle()
        );
        $this->assertEquals( $sections, $field->getSections() );
        $this->assertEquals( $relations, $field->getSectionRelations() );
        $this->assertFalse( $field->addSection( $section ) );
        $this->assertEquals( count( $sections ), $field->getSectionRelations()->count() );
        $this->assertEquals( count( $sections ), count( $field->getSections()) );
        $this->assertContains( $section, $field->getSections() );
    }

    public function testRemoveSection()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $field->addSection( $section );
            $sectionFields[] = new SectionField( $section, $field );
        }

        $lastSection = $sections[count( $sections )-1];
        $field->removeSection( $lastSection );

        $this->assertEquals( count( $sections ), count( $field->getSectionRelations()) );
        $this->assertEquals( count( $sections ), count( $field->getSections()) );
        $this->assertEquals( count( $sections ), 4 );
        $this->assertEquals( count( $field->getSections()), 4 );
        $this->assertContains( $section, $field->getSections( TRUE ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSections
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSectionRelations
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSectionRelations
     */
    public function testSectionRelations()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $field->setSectionRelations( $relations );

        $this->assertEquals( $relations, $field->getSectionRelations() );
        $this->assertEquals( $sections, $field->getSections() );
        $this->assertEquals( count( $sections ), count( $sectionFields ) );
        $this->assertEquals( $relations->first()->getSection(), reset( $field->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSectionRelation
     */
    public function testSectionRelation()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++ )
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $field->setSectionRelations( $relations );

        $relation = $relations[2];
        $section = $relation->getSection();

        $this->assertEquals( $relation, $field->getSectionRelation( $section ) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addSectionRelation
     */
    public function testAddSectionRelation()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $field->setSectionRelations( $relations );

        $section = new Section( 'new Section', 'new Section added `a postoriori`' );
        $relation = new SectionField( $section, $field );
        $field->addSectionRelation( $relation );

        $this->assertEquals( $relations, $field->getSectionRelations() );
        $this->assertContains( $relation->getSection(), $field->getSections() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertFalse( $field->addSectionRelation( $relation ) );
        $this->assertContains( $relation, $field->getSectionRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeSectionRelation
     */
    public function testRemoveSectionRelation()
    {
        $field = new Field();
        $sections = array();
        $sectionFields = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section(
                'title for section ' . $i,
                'description for section ' . $i
            );
            $sections[] = $section;
            $sectionFields[] = new SectionField( $section, $field );
        }
        $relations = new ArrayCollection( $sectionFields );
        $field->setSectionRelations( $relations );
        $relation3 = $relations->get( count( $relations ) - 1 );
        $field->removeSectionRelation( $relation3 );

        $this->assertEquals( $relations, $field->getSectionRelations() );
        $this->assertEquals( count( $relations ), count( $field->getSectionRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $field->getSectionRelations() );
        $this->assertNotContains( $relation3->getSection(), $field->getSections( FALSE ) );
        $this->assertContains( $relation3->getSection(), $field->getSections() );
    }
}
