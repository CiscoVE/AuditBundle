<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use WG\AuditBundle\Entity\AuditForm;
use WG\AuditBundle\Entity\AuditFormField;
use WG\AuditBundle\Entity\AuditScore;

/**
 * @ORM\Entity
 * @ORM\Table(name="wgaudit")
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
     * @ORM\ManyToOne(targetEntity="AuditForm")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $auditForm;

    /**
     * @ORM\Column(type="integer", name="audit_reference_id", nullable=true)
     */
    protected $auditReference;

    /**
     * @ORM\Column(type="integer", name="auditing_user_id", nullable=true)
     */
    protected $auditingUser;

    /**
     * @ORM\Column(type="integer", name="control_user_id", nullable=true)
     */
    protected $controlUser;

    /**
     * @var boolean
     */
    protected $failed;

    /**
     * @ORM\OneToMany(targetEntity="WG\AuditBundle\Entity\AuditScore", mappedBy="audit")
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
     * @param \WG\AuditBundle\Entity\AuditScore $score
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
     * @param \WG\AuditBundle\Entity\AuditScore $score
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
    public function setAuditForm($auditForm)
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
    public function setAuditReference($auditReference)
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
    public function setAuditingUser($auditingUser)
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
     * Set controlUser
     *
     * @param integer $controlUser
     * @return Audit
     */
    public function setControlUser($controlUser)
    {
        $this->controlUser = $controlUser;

        return $this;
    }

    /**
     * Get controlUser
     *
     * @return integer
     */
    public function getControlUser()
    {
        return $this->controlUser;
    }

    /**
     * Set failed
     *
     * @param boolean $failed
     * @return Audit
     */
    public function setFailed($failed)
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
    public function setCreatedAt($createdAt)
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

    public function getScoreForField( AuditFormField $field )
    {
        $scores = $this->getScores();
        foreach ( $scores as $score )
        {
            if ( $field->getId() == $score->getField()->getId() )
            {
                return $score;
            }
        }
        return false;
    }

    public function getResultForSection( $section )
    {
        $fields = $section->getFields();
        $fieldCount = count( $fields );
        if ( 0 == $fieldCount ) return 100;
        $achievedPercentages = 0;
        foreach ( $fields as $field )
        {
            $score = $this->getScoreForField( $field );
            if ( !$score ) continue;
            if ( $field->getFatal() == true )
            {
                if ( $score->getScore() == AuditScore::NO )
                {
                    $this->setFailed( true );
                    continue;
                }
            }
            $achievedPercentages += $score->getWeightPercentage();
        }
        return $achievedPercentages / $fieldCount;
    }

    public function getTotalResult()
    {
        $sections = $this->getAuditForm()->getSections();
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
        return $totalPercent;
    }
}