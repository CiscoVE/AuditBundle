<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use CiscoSystems\AuditBundle\Entity\AuditScore;
use Doctrine\Common\Collections\ArrayCollection;

class AuditFormFieldTest extends \PHPUnit_Framework_TestCase
{
    protected $section;
    protected $field;

    protected function setUp()
    {
        parent::setUp();
        $this->field = new AuditFormField();
        $this->section = new AuditFormSection();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setTitle
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getTitle
     */
    public function testTitle()
    {
        $title = 'test title string';
        $this->field->setTitle( $title );

        $this->assertEquals( $title, $this->field->getTitle() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setDescription
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getDescription
     */
    public function testDescription()
    {
        $description = 'test description string';
        $this->field->setDescription( $description );

        $this->assertEquals( $description, $this->field->getDescription() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setWeigth
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::GetWeigth
     */
    public function testWeight()
    {
        $weight = 5;
        $this->field->setWeight( $weight );

        $this->assertEquals( $weight, $this->field->getWeight() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getFlag
     */
    public function testFlag()
    {
        $flag = true;
        $this->field->setFlag( $flag );

        $this->assertEquals( $flag, $this->field->getFlag() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setPosition
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getPosition
     */
    public function testPosition()
    {
        $position = 1;
        $this->field->setPosition( $position );

        $this->assertEquals( $position, $this->field->getPosition() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setSection
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getSection
     */
    public function testSection()
    {
        $section = $this->section;
        $this->field->setSection( $section );

        $this->assertEquals( $section, $this->field->getSection() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setSlug
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getSlug
     */
    public function testSlug()
    {
        $slug = 'test-field-slug';
        $this->field->setSlug( $slug );

        $this->assertEquals( $slug, $this->field->getSlug() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setScores
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getScores
     */
    public function testScores()
    {
        $scores = array(
            'Y'     => 'correct answer',
            'N'     => 'incorrect answer',
            'A'     => 'acceptable answer',
            'N\A'   => 'non-applicable answer'
        );
        $this->field->setScores( $scores );

        $this->assertEquals( $scores, $this->field->getScores() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::addScore
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
        $this->field->setScores( $scores );
        $this->field->addScore( $score, $label );

        $actualScores = $this->field->getScores();

        $this->assertEquals( $label, $actualScores[$score] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setAuditScores
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getAuditScores
     */
    public function testAuditScores()
    {
        $auditscore1 = new AuditScore();
        $auditscore2 = new AuditScore();
        $auditscore3 = new AuditScore();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setAuditScores( $auditscores );

        $this->assertEquals( count( $auditscores ), count( $this->field->getAuditscores()) );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::addAuditScore
     */
    public function testAddAuditScore()
    {
        $auditscore1 = new AuditScore();
        $auditscore2 = new AuditScore();
        $auditscore3 = new AuditScore();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setAuditScores( $auditscores );

        $auditscore = new AuditScore();
        $this->field->addAuditScore( $auditscore );
        $actualScores = $this->field->getAuditscores();

        $this->assertEquals( $auditscore, $actualScores[( count( $actualScores ) -1 )] );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::removeAuditScore
     */
    public function testRemoveAuditScore()
    {
        $auditscore1 = new AuditScore();
        $auditscore2 = new AuditScore();
        $auditscore3 = new AuditScore();
        $auditscores = new ArrayCollection( array( $auditscore1, $auditscore2, $auditscore3 ));
        $this->field->setAuditScores( $auditscores );

        $this->field->removeAuditScore( $auditscore3 );
        $auditscores->removeElement( $auditscore3 );

        $this->assertNotContains( $auditscore3, $this->field->getAuditscores() );
        $this->assertEquals( $auditscores, $this->field->getAuditscores() );
    }
}