<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\AuditForm;
use CiscoSystems\AuditBundle\Entity\AuditScore;
use Doctrine\Common\Collections\ArrayCollection;

class AuditTest extends \PHPUnit_Framework_TestCase
{
    protected $audit;

    protected function setUp()
    {
        parent::setUp();
        $this->audit = new Audit();
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setAuditForm
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getAuditForm
     */
    public function testAuditForm()
    {
        $form = new AuditForm();
        $this->audit->setAuditForm( $form );

        $this->assertEquals( $form, $this->audit->getAuditForm() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setAuditReference
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getAuditReference
     */
    public function testAuditReference()
    {
        // need to be tested on implementation of the ReferenceInterface
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setAuditingUser
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getAuditingUser
     */
    public function testAuditingUser()
    {
        // need to be tested on implementation of the UserInterface
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setFlag
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getFlag
     */
    public function testFlag()
    {
        $flag = TRUE;
        $this->audit->setFlag( $flag );

        $this->assertEquals( $flag, $this->audit->getFlag() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setTotalScore
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getTotalScore
     */
    public function testTotalScore()
    {
        $score = 75.50;
        $this->audit->setTotalScore( $score );

        $this->assertEquals( $score, $this->audit->getTotalScore() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setScores
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getScores
     */
    public function testScores()
    {
        $score1 = new AuditScore();
        $score2 = new AuditScore();
        $score3 = new AuditScore();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $this->assertEquals( count( $scores ), count( $this->audit->getScores()) );

    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::addScore
     */
    public function testAddScore()
    {
        $score1 = new AuditScore();
        $score2 = new AuditScore();
        $score3 = new AuditScore();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $score = new AuditScore();
        $this->audit->addScore( $score );

        $this->assertEquals( $scores, $this->audit->getScores() );
        $this->assertContains( $score, $this->audit->getScores() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::removeScore
     */
    public function testRemoveScore()
    {
        $score1 = new AuditScore();
        $score2 = new AuditScore();
        $score3 = new AuditScore();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $this->audit->removeScore( $score3 );
        $scores->removeElement( $score3 );

        $this->assertEquals( $scores, $this->audit->getScores() );
        $this->assertNotContains( $score3, $this->audit->getScores() );
    }
}