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

//    public function testSections()
//    {
//        $sections = array();
//        for( $i = 1; $i < 4; $i++)
//        {
//            $section = new Section(
//                        'title for section ' . $i,
//                        'description for section ' . $i
//                       );
//            $sections[] = $section;
//            $this->field->addSectionRelation( new SectionField( $section, $this->field ));
//        }
//        $section = new Section( 'new section', 'this is a new section' );
//        $this->field->addSectionRelation( new SectionField( $section, $this->field ));
//        $sections[] = $section;
//
//        $this->assertContains( $section, $this->field->getSections() );
//        $this->assertEquals( $sections, $this->field->getSections() );
//
//    }

//    public function testAddSection()
//    {
//        $sections = array();
//        for( $i = 1; $i < 4; $i++)
//        {
//            $section = new Section(
//                        'title for section ' . $i,
//                        'description for section ' . $i
//                       );
//            $sections[] = $section;
//            $this->field->addSectionRelation( new SectionField( $section, $this->field ));
//        }
//        $relations = $this->field->getSectionRelations();
//
//        $section = new Section( 'new section', 'this is a new section' );
//        $this->field->addSection( $section );
//        $sections[] = $section;
//
//        $this->assertEquals( $relations, $this->field->getSectionRelations() );
//        $this->assertFalse( $this->field->addSection( $section ) );
//        $this->assertEquals( count( $this->field->getSections() ), 3 );
//        $this->assertEquals( count( $sections ), 3 );

        //actual = 4, expected = 3
//        $this->assertEquals( count( $sections ), count( $this->field->getSectionRelations()) );
//        $this->assertEquals( count( $sections ), count( $this->field->getSections()) );
//        $this->assertFalse( $this->field->addSection( $section ) );

//        $this->assertContains( $section, $this->field->getSections() );
//    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSections
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSectionRelations
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSectionRelations
     */
    public function testSectionRelations()
    {
        $sections = array();
        $relations = array();
        for( $i = 1; $i < 4; $i++)
        {
            $section = new Section();
            $section->setTitle( 'title for section ' . $i )
                    ->setDescription( 'description for section ' . $i );
            $sections[] = $section;
            $relations[] = new SectionField( $section, $this->field );
        }
        $collection = new ArrayCollection( $relations );
        $this->field->setSectionRelations( $collection );

        $this->assertEquals( $collection, $this->field->getSectionRelations() );
        $this->assertEquals( $sections, $this->field->getSections() );
        $this->assertEquals( count( $sections ), count( $relations ) );
        $this->assertEquals( $collection->first()->getSection(), reset( $this->field->getSections()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addSectionRelation
     */
    public function testAddSectionRelation()
    {
        $relation1 = new SectionField( new Section(), $this->field );
        $relation2 = new SectionField( new Section(), $this->field );
        $relation3 = new SectionField( new Section(), $this->field );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->field->setSectionRelations( $relations );

        $relation = new SectionField( new Section(), $this->field );
        $this->field->addSectionRelation( $relation );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertContains( $relation->getSection(), $this->field->getSections() );
        $this->assertFalse( $relation->getArchived() );
        $this->assertContains( $relation, $this->field->getSectionRelations() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeSectionRelation
     */
    public function testRemoveSectionRelation()
    {
        $relation1 = new SectionField( new Section(), $this->field );
        $relation2 = new SectionField( new Section(), $this->field );
        $relation3 = new SectionField( new Section(), $this->field );
        $relations = new ArrayCollection( array( $relation1, $relation2, $relation3 ));
        $this->field->setSectionRelations( $relations );
        $this->field->removeSectionRelation( $relation3 );

        $this->assertEquals( $relations, $this->field->getSectionRelations() );
        $this->assertEquals( count( $relations ), count( $this->field->getSectionRelations() ));
        $this->assertTrue( $relation3->getArchived() );
        $this->assertContains( $relation3, $this->field->getSectionRelations() );
        $this->assertNotContains( $relation3->getSection(), $this->field->getSections() );
    }
}
