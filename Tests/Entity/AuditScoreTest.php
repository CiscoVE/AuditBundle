<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\AuditScore;

class AuditScoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnCorrectWeightPercentage()
    {
        $score = new AuditScore();
        $score->setScore( AuditScore::ACCEPTABLE );
        $this->assertEquals( 50, $score->getWeightPercentage() );
        $score->setScore( AuditScore::NO );
        $this->assertEquals( 0, $score->getWeightPercentage() );
        $score->setScore( AuditScore::NOT_APPLICABLE );
        $this->assertEquals( 100, $score->getWeightPercentage() );
        $score->setScore( AuditScore::YES );
        $this->assertEquals( 100, $score->getWeightPercentage() );
    }
}
