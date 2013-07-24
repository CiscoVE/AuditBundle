<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Score;

class AuditScoreTest extends \PHPUnit_Framework_TestCase
{
    private $audit;
    private $field;
    private $auditscore;

    protected function setUp()
    {
        parent::setUp();
        $this->audit = new Audit();
        $this->field = new Field();
        $this->auditscore = new Score();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setMark
     * @covers CiscoSystems\AuditBundle\Entity\Field::getMark
     */
    public function testScore()
    {
        $score = 'test score string';
        $this->auditscore->setMark( $score );

        $this->assertEquals( $score, $this->auditscore->getMark() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setComment
     * @covers CiscoSystems\AuditBundle\Entity\Field::getComment
     */
    public function testComment()
    {
        $comment = 'test comment string';
        $this->auditscore->setComment( $comment );

        $this->assertEquals( $comment, $this->auditscore->getComment() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setAudit
     * @covers CiscoSystems\AuditBundle\Entity\Field::getAudit
     */
    public function testAudit()
    {
        $audit = $this->audit;
        $this->auditscore->setAudit( $audit );

        $this->assertEquals( $audit, $this->auditscore->getAudit() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::setField
     * @covers CiscoSystems\AuditBundle\Entity\Field::getField
     */
    public function testField()
    {
        $field = $this->field;
        $this->auditscore->setField( $field );

        $this->assertEquals( $field, $this->auditscore->getField() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Field::getWeightPercentageForScore
     */
    public function testWeightPercentage()
    {
        $this->auditscore->setMark( Score::ACCEPTABLE );
        $this->assertEquals( 50,    Score::getWeightPercentageForScore( Score::ACCEPTABLE ));

        $this->auditscore->setMark( Score::NO );
        $this->assertEquals( 0,     Score::getWeightPercentageForScore( Score::NO ));

        $this->auditscore->setMark( Score::NOT_APPLICABLE );
        $this->assertEquals( 100,   Score::getWeightPercentageForScore( Score::NOT_APPLICABLE ));

        $this->auditscore->setMark( Score::YES );
        $this->assertEquals( 100,   Score::getWeightPercentageForScore( Score::YES ));
    }
}
