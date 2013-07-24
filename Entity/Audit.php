<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Score;
use CiscoSystems\AuditBundle\Model\UserInterface;
use CiscoSystems\AuditBundle\Model\ReferenceInterface;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditRepository")
 * @ORM\Table(name="cisco_audit__audit")
 */
class Audit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="audits")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $form;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\ReferenceInterface")
     * @ORM\JoinColumn(name="reference_id",referencedColumnName="id")
     */
    protected $reference;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\UserInterface")
     * @ORM\JoinColumn(name="auditing_user_id",referencedColumnName="id")
     */
    protected $auditor;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $flag;

    /**
     * @ORM\Column(type="float",nullable=true,name="mark")
     */
    protected $mark;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Score",mappedBy="audit")
     */
    protected $scores;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    function __construct()
    {
        $this->scores = new ArrayCollection();
    }

    /**
     * Get total score
     *
     * @return integer
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set total score
     *
     * @param integer $mark
     */
    public function setMark( $mark )
    {
        $this->mark = $mark;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set auditForm
     *
     * @param string $form
     */
    public function setForm( \CiscoSystems\AuditBundle\Entity\Form $form = null)
    {
        $this->form = $form;
    }

    /**
     * Get auditForm
     *
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set reference
     *
     * @param \CiscoSystems\AuditBundle\Model\ReferenceInterface $reference
     */
    public function setReference( \CiscoSystems\AuditBundle\Model\ReferenceInterface $reference )
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return \CiscoSystems\AuditBundle\Model\ReferenceInterface
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set auditingUser
     *
     * @param \CiscoSystems\AuditBundle\Model\UserInterface $auditor
     */
    public function setAuditor( \CiscoSystems\AuditBundle\Model\UserInterface $auditor )
    {
        $this->auditor = $auditor;
    }

    /**
     * Get auditingUser
     *
     * @return \CiscoSystems\AuditBundle\Model\UserInterface
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Get flag
     *
     * @return boolean
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set flag
     *
     * @param boolean $flag
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt( \DateTime $createdAt )
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set scores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     */
    public function setScores( \Doctrine\Common\Collections\ArrayCollection $scores = NULL )
    {
        $this->scores = $scores;
    }

    /**
     * Get scores
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Add a score to collection scores and set audit in the score instance
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     */
    public function addScore( \CiscoSystems\AuditBundle\Entity\Score $score )
    {
        if( !$this->scores->contains( $score ) )
        {
            $score->setAudit( $this );
            $this->scores->add( $score );

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Remove score
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     */
    public function removeScore( \CiscoSystems\AuditBundle\Entity\Score $score )
    {
        if( $this->scores->contains( $score ))
        {
            $this->scores->removeElement( $score );

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Remove all score from ArrayCollection $this->scores
     */
    public function removeAllScores()
    {
        foreach( $this->scores as $score )
        {
            $this->removeScore( $score );
        }
    }

    /**
     * Get Score for Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     * @return \CiscoSystems\AuditBundle\Entity\Score
     */
//    public function getScoreForField( \CiscoSystems\AuditBundle\Entity\Field $field )
//    {
//        $scores = $this->getScores();
//
//        foreach ( $scores as $score )
//        {
//            if ( null !== $score->getField() && $field === $score->getField() )
//            {
//                return $score;
//            }
//        }
//
//        return FALSE;
//    }

    /**
     * Get Score for Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     * @return integer
     */
//    public function getResultForSection( \CiscoSystems\AuditBundle\Entity\Section $section )
//    {
//        $fields = $section->getFields();
//        $fieldCount = count( $fields );
//
//        if ( 0 == $fieldCount ) return 100;
//        $achievedPercentages = 0;
//
//        foreach ( $fields as $field )
//        {
//            $score = $this->getScoreForField( $field );
//
//            if ( !$score )
//            {
//                $score = new Score();
//                $score->setScore( Score::YES );
//            }
//            $achievedPercentages += $score->getWeightPercentage();
//        }
//        return number_format( $achievedPercentages / $fieldCount, 2, '.', '' );
//    }

//    public function findFlagForSection( \CiscoSystems\AuditBundle\Entity\Section $section )
//    {
//        foreach ( $section->getFields() as $field )
//        {
//            if ( $field->getFlag() == true &&  $this->getScoreForField( $field )->getMark() == Score::NO )
//            {
//                $section->setFlag( true );
//            }
//        }
//    }

    /**
     * Get global score
     *
     * @return integer
     */
//    public function getTotalResult()
//    {
//        if ( null !== $auditform = $this->getForm() )
//        {
//            $sections = $auditform->getSections();
//            $count = count( $sections );
//            if ( 0 == $count ) return 100;
//            $totalPercent = 0;
//            $divisor = 0;
//            $this->setFlag( false );
//
//            foreach ( $sections as $section )
//            {
//                $percent = $this->getResultForSection( $section );
//                $weight = $section->getWeight();
//                $this->findFlagForSection( $section );
//
//                if ( $section->getFlag() ) $this->setFlag( true );
//
//                $divisor += $weight;
//
//                // check the section for flag not set and section's weight > 0
//                if( $section->getFlag() === false && $divisor > 0 )
//                {
//                    $totalPercent = $totalPercent * ( $divisor - $weight ) / $divisor + $percent * $weight / $divisor;
//                }
//            }
//            return number_format( $totalPercent, 2, '.', '' );
//        }
//        else return 0;
//    }

    /**
     * Get global weight
     *
     * @return integer
     */
//    public function getTotalWeight()
//    {
//        $weight = 0;
//        $sections = $this->getForm()->getSections();
//
//        foreach ( $sections as $section )
//        {
//            $weight += $section->getWeight();
//        }
//        return $weight;
//    }
}
