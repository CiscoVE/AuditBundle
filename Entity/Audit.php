<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\AuditForm;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditScore;

/**
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditForm")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $auditForm;

    /**
     * @ORM\Column(type="integer", name="audit_reference_id", nullable=true)
     */
    protected $auditReference;

    /**     
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\UserInterface")
     * @ORM\JoinColumn(name="auditing_user_id",referencedColumnName="id")
     */
    protected $auditingUser;

    /**
     * @var boolean
     */
    protected $failed;

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
     * @return type
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Add a score
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     * @return Audit
     */
    public function addScore( AuditScore $score )
    {
        $score->setAudit( $this );
        $this->scores[ ] = $score;

        return $this;
    }

    /**
     * Remove score
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     */
    public function removeScore( AuditScore $score )
    {
        $this->scores->removeElement( $score );
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
     * @return Audit
     */
    public function setAuditForm( $auditForm )
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
     * @param integer $auditReference
     * @return Audit
     */
    public function setAuditReference( $auditReference )
    {
        $this->auditReference = $auditReference;

        return $this;
    }

    /**
     * Get auditReference
     *
     * @return integer
     */
    public function getAuditReference()
    {
        return $this->auditReference;
    }

    /**
     * Set auditingUser
     *
     * @param integer $auditingUser
     * @return Audit
     */
    public function setAuditingUser( $auditingUser )
    {
        $this->auditingUser = $auditingUser;

        return $this;
    }

    /**
     * Get auditingUser
     *
     * @return integer
     */
    public function getAuditingUser()
    {
        return $this->auditingUser;
    }

    /**
     * Set failed
     *
     * @param boolean $failed
     * @return Audit
     */
    public function setFailed( $failed )
    {
        $this->failed = $failed;

        return $this;
    }

    /**
     * Get failed
     *
     * @return boolean
     */
    public function getFailed()
    {
        return $this->failed;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Audit
     */
    public function setCreatedAt( $createdAt )
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
     * @return $score
     */
    public function getScoreForField( AuditFormField $field )
    {
        $scores = $this->getScores();
        foreach ( $scores as $score )
        {
            if ( null !== $score->getField() && $field === $score->getField())
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
     * @return int
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
            if ( $field->getFatal() == true )
            {
                if ( $score->getScore() == AuditScore::NO )
                {
                    $this->setFailed( true );
                    continue;
                }
            }
            else
                $achievedPercentages += $score->getWeightPercentage();
        }
        return number_format($achievedPercentages / $fieldCount, 2, '.', '');
    }

    // TODO: take into account the weight of 1 for fields where getFatal() == true
    /**
     * Get global score
     * 
     * @return int
     */
    public function getTotalResult()
    {
        if( null !== $auditform = $this->getAuditForm())
        {
            $sections = $auditform->getSections();
            if ( 0 == count( $sections ) ) return 100;
            $totalPercent = 0;
            $divisor = 0;
            foreach ( $sections as $section )
            {
                $percent = $this->getResultForSection( $section );
                $weight  = $section->getWeight();
                $divisor += $weight;
                $totalPercent = $totalPercent * ( $divisor - $weight ) / $divisor + $percent * $weight / $divisor;
            }
            return number_format($totalPercent, 2, '.', '');
        }
        else
            return 0;
    }

    /**
     * Get global weight
     * 
     * @return int
     */
    public function getTotalWeight()
    {
        $weight = 0;
        $sections = $this->getAuditForm()->getSections();
        foreach( $sections as $section )
        {
              $weight += $section->getWeight();
        }
        return $weight;
    }
}