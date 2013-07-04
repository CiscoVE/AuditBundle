<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditScore;

class AuditScoreTest extends \PHPUnit_Framework_TestCase
{
    private $audit;
    private $field;
    private $auditscore;

    protected function setUp()
    {
        parent::setUp();
        $this->audit = new Audit();
        $this->field = new AuditFormField();
        $this->auditscore = new AuditScore();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setScore
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getScore
     */
    public function testScore()
    {
        $score = 'test score string';
        $this->auditscore->setScore( $score );

        $actual = $this->auditscore->getScore();
        $expected = $score;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setComment
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getComment
     */
    public function testComment()
    {
        $comment = 'test comment string';
        $this->auditscore->setComment( $comment );

        $actual = $this->auditscore->getComment();
        $expected = $comment;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setAudit
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getAudit
     */
    public function testAudit()
    {
        $audit = $this->audit;
        $this->auditscore->setAudit( $audit );

        $actual = $this->auditscore->getAudit();
        $expected = $audit;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::setField
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getField
     */
    public function testField()
    {
        $field = $this->field;
        $this->auditscore->setField( $field );

        $actual = $this->auditscore->getField();
        $expected = $field;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\AuditFormField::getWeightPercentageForScore
     */
    public function testWeightPercentage()
    {
        $this->auditscore->setScore( AuditScore::ACCEPTABLE );
        $this->assertEquals( 50,    AuditScore::getWeightPercentageForScore( AuditScore::ACCEPTABLE ));

        $this->auditscore->setScore( AuditScore::NO );
        $this->assertEquals( 0,     AuditScore::getWeightPercentageForScore( AuditScore::NO ));

        $this->auditscore->setScore( AuditScore::NOT_APPLICABLE );
        $this->assertEquals( 100,   AuditScore::getWeightPercentageForScore( AuditScore::NOT_APPLICABLE ));

        $this->auditscore->setScore( AuditScore::YES );
        $this->assertEquals( 100,   AuditScore::getWeightPercentageForScore( AuditScore::YES ));
    }
}
