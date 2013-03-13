<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditScore;
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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditForm", inversedBy="audits")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $auditForm;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\ReferenceInterface")
     * @ORM\JoinColumn(name="reference_id",referencedColumnName="id")
     */
    protected $auditReference;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\UserInterface")
     * @ORM\JoinColumn(name="auditing_user_id",referencedColumnName="id")
     */
    protected $auditingUser;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $flag;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $totalScore;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditScore", mappedBy="audit")
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
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function addScore( \CiscoSystems\AuditBundle\Entity\AuditScore $score )
    {
        if( !$this->scores->contains( $score ) )
        {
            $score->setAudit( $this );
            $this->scores->add( $score );
        }
        return $this;
    }

    /**
     * Remove score
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     */
    public function removeScore( \CiscoSystems\AuditBundle\Entity\AuditScore $score )
    {
        $this->scores->removeElement( $score );
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
     * Get total score
     *
     * @return integer
     */
    public function getTotalScore()
    {
        return $this->totalScore;
    }

    /**
     * Set total score
     *
     * @param integer $totalScore
     */
    public function setTotalScore( $totalScore )
    {
        $this->totalScore = $totalScore;
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
     * @param string $auditForm
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setAuditForm( \CiscoSystems\AuditBundle\Entity\AuditForm $auditForm = null)
    {
        $this->auditForm = $auditForm;

        return $this;
    }

    /**
     * Get auditForm
     *
     * @return string
     */
    public function getAuditForm()
    {
        return $this->auditForm;
    }

    /**
     * Set auditReference
     *
     * @param \CiscoSystems\AuditBundle\Model\ReferenceInterface $auditReference
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setAuditReference( ReferenceInterface $auditReference )
    {
        $this->auditReference = $auditReference;

        return $this;
    }

    /**
     * Get auditReference
     *
     * @return \CiscoSystems\AuditBundle\Model\ReferenceInterface
     */
    public function getAuditReference()
    {
        return $this->auditReference;
    }

    /**
     * Set auditingUser
     *
     * @param \CiscoSystems\AuditBundle\Model\UserInterface $auditingUser
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setAuditingUser( UserInterface $auditingUser )
    {
        $this->auditingUser = $auditingUser;

        return $this;
    }

    /**
     * Get auditingUser
     *
     * @return \CiscoSystems\AuditBundle\Model\UserInterface
     */
    public function getAuditingUser()
    {
        return $this->auditingUser;
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
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setCreatedAt( \DateTime $createdAt )
    {
        $this->createdAt = $createdAt;

        return $this;
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
     * Get Score for Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormField $field
     * @return \CiscoSystems\AuditBundle\Entity\AuditScore
     */
    public function getScoreForField( AuditFormField $field )
    {
        $scores = $this->getScores();

        foreach ( $scores as $score )
        {
            if ( null !== $score->getField() && $field === $score->getField() )
            {
                return $score;
            }
        }
        return false;
    }

    /**
     * Get Score for Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     * @return integer
     */
    public function getResultForSection( AuditFormSection $section )
    {
        $fields = $section->getFields();
        $fieldCount = count( $fields );

        if ( 0 == $fieldCount ) return 100;
        $achievedPercentages = 0;

        foreach ( $fields as $field )
        {
            $score = $this->getScoreForField( $field );

            if ( !$score )
            {
                $score = new AuditScore();
                $score->setScore( AuditScore::YES );
            }

            if ( $field->getFlag() == true )
            {
                if ( $score->getScore() == AuditScore::NO )
                {
                    $this->setFlag( true );
                    continue;
                }
            }
//            else
                $achievedPercentages += $score->getWeightPercentage();
        }
        return number_format( $achievedPercentages / $fieldCount, 2, '.', '' );
    }

    /**
     * Get global score
     *
     * @return integer
     */
    public function getTotalResult()
    {
        if ( null !== $auditform = $this->getAuditForm() )
        {
            $sections = $auditform->getSections();
            $count = count( $sections );
            if ( 0 == $count ) return 100;
            $totalPercent = 0;
            $divisor = 0;
            $this->setFlag( false );

            foreach ( $sections as $section )
            {
                if( $section->getFlat() === false )
                {
                    $percent = $this->getResultForSection( $section );
                    $weight = $section->getWeight();
                    $divisor += $weight;
                    $totalPercent = $totalPercent * ( $divisor - $weight ) / $divisor + $percent * $weight / $divisor;
                }
                else
                    $this->setFlag ( true );
            }
            return number_format( $totalPercent, 2, '.', '' );
        }
        else return 0;
    }

    /**
     * Get global weight
     *
     * @return integer
     */
    public function getTotalWeight()
    {
        $weight = 0;
        $sections = $this->getAuditForm()->getSections();

        foreach ( $sections as $section )
        {
            $weight += $section->getWeight();
        }
        return $weight;
    }
}
