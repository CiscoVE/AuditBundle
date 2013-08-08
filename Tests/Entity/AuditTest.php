<?php

namespace CiscoSystems\AuditBundle\Tests\Entity;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Score;
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
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setForm
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getForm
     */
    public function testForm()
    {
        $form = new Form();
        $this->audit->setForm( $form );

        $this->assertEquals( $form, $this->audit->getForm() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setReference
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getReference
     */
    public function testReference()
    {
        $reference = $this->getMock( 'CiscoSystems\AuditBundle\Model\ReferenceInterface' );
        $this->audit->setReference( $reference );

        $this->assertEquals( $reference, $this->audit->getReference() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setAuditor
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getAuditor
     */
    public function testAuditor()
    {
        $auditor = $this->getMock( 'CiscoSystems\AuditBundle\Model\UserInterface' );
        $this->audit->setAuditor( $auditor );

        $this->assertEquals( $auditor, $this->audit->getAuditor() );
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
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setMark
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getMark
     */
    public function testMark()
    {
        $score = 75.50;
        $this->audit->setMark( $score );

        $this->assertEquals( $score, $this->audit->getMark() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::setScores
     * @covers CiscoSystems\AuditBundle\Entity\Audit::getScores
     */
    public function testScores()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $this->assertEquals( count( $scores ), count( $this->audit->getScores()) );

    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::addScore
     */
    public function testAddScore()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $score = new Score();
        $this->audit->addScore( $score );

        $this->assertEquals( $scores, $this->audit->getScores() );
        $this->assertContains( $score, $this->audit->getScores() );
    }

    /**
     * @covers CiscoSystems\AuditBundle\Entity\Audit::removeScore
     */
    public function testRemoveScore()
    {
        $score1 = new Score();
        $score2 = new Score();
        $score3 = new Score();
        $scores = new ArrayCollection( array( $score1, $score2, $score3 ));
        $this->audit->setScores( $scores );

        $this->audit->removeScore( $score3 );
        $scores->removeElement( $score3 );

        $this->assertEquals( $scores, $this->audit->getScores() );
        $this->assertNotContains( $score3, $this->audit->getScores() );
    }
}