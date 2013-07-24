<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Score;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormFieldTest extends \PHPUnit_Framework_TestCase
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
     * @covers CiscoSystems\AuditBundle\Entity\Field::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\Field::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->field->setPosition( $position );

        $this->assertEquals( $position, $this->field->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSection
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSection
     */
    public function testSection()
    {
        $section = $this->section;
        $this->field->setSection( $section );

        $this->assertEquals( $section, $this->field->getSection() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setSlug
     * @covers CiscoSystems\AuditBundle\Entity\Field::getSlug
     */
    public function testSlug()
    {
        $slug = 'test-field-slug';
        $this->field->setSlug( $slug );

        $this->assertEquals( $slug, $this->field->getSlug() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setChoices
     * @covers CiscoSystems\AuditBundle\Entity\Field::getChoices
     */
    public function testScores()
    {
        $scores = array(
            'Y'     => 'correct answer',
            'N'     => 'incorrect answer',
            'A'     => 'acceptable answer',
            'N\A'   => 'non-applicable answer'
        );
        $this->field->setChoices( $scores );

        $this->assertEquals( $scores, $this->field->getChoices() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addChoice
     */
    public function testScore()
    {
        $scores = array(
            'Y'     => 'correct answer',
            'N'     => 'incorrect answer',
            'A'     => 'acceptable answer',
            'N\A'   => 'non-applicable answer'
        );
        $score = 'T';
        $label = 'test answer item';
        $this->field->setChoices( $scores );
        $this->field->addChoice( $score, $label );

        $actualScores = $this->field->getChoices();

        $this->assertEquals( $label, $actualScores[$score] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setScores
     * @covers CiscoSystems\AuditBundle\Entity\Field::getScores
     */
    public function testAuditScores()
    {
        $auditscore1 = new Score();
        $auditscore2 = new Score();
        $auditscore3 = new Score();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setScores( $auditscores );

        $this->assertEquals( count( $auditscores ), count( $this->field->getAuditscores()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::addAuditScore
     */
    public function testAddAuditScore()
    {
        $auditscore1 = new Score();
        $auditscore2 = new Score();
        $auditscore3 = new Score();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setScores( $auditscores );

        $auditscore = new Score();
        $this->field->addAuditScore( $auditscore );
        $actualScores = $this->field->getAuditscores();

        $this->assertEquals( $auditscore, $actualScores[( count( $actualScores ) -1 )] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::removeAuditScore
     */
    public function testRemoveAuditScore()
    {
        $auditscore1 = new Score();
        $auditscore2 = new Score();
        $auditscore3 = new Score();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setScores( $auditscores );

        $this->field->removeAuditScore( $auditscore3 );
        $auditscores->removeElement( $auditscore3 );

        $this->assertNotContains( $auditscore3, $this->field->getAuditscores() );
        $this->assertEquals( $auditscores, $this->field->getAuditscores() );
    }
}