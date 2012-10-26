<?php

namespace WG\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wgauditscore")
 */
class AuditScore
{
    const YES = "Y";
    const NO  = "N";
    const ACCEPTABLE = "A";
    const NOT_APPLICABLE = "N/A";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Audit")
     */
    protected $audit;

    /**
     * @ORM\ManyToOne(targetEntity="AuditFormField")
     */
    protected $field;

    /**
     * @ORM\Column(type="string")
     */
    protected $score;

    /**
     * @var interger
     */
    protected $weightPercentage;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $comment;

    public function getWeightPercentage()
    {
        $this->weightPercentage = self::getWeightPercentageForScore( $this->score );
        return $this->weightPercentage;
    }

    static public function getWeightPercentageForScore( $score )
    {
        switch($score)
        {
            case AuditScore::YES:
            case AuditScore::NOT_APPLICABLE: return 100;
            case AuditScore::ACCEPTABLE: return 50;
            case AuditScore::NO: break;
        }
        return 0;
    }

    public function calculateWeight()
    {
        $weight = $this->field->getWeight();
        switch( $this->score )
        {
            case AuditScore::YES:
            case AuditScore::NOT_APPLICABLE: return $weight;
            case AuditScore::ACCEPTABLE: return $weight / 2;
            case AuditScore::NO: return 0;
        }
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
     * Set score
     *
     * @param string $score
     * @return AuditScore
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return AuditScore
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set audit
     *
     * @param WG\AuditBundle\Entity\Audit $audit
     * @return AuditScore
     */
    public function setAudit(\WG\AuditBundle\Entity\Audit $audit = null)
    {
        $this->audit = $audit;

        return $this;
    }

    /**
     * Get audit
     *
     * @return WG\AuditBundle\Entity\Audit
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * Set field
     *
     * @param WG\AuditBundle\Entity\AuditFormField $field
     * @return AuditScore
     */
    public function setField(\WG\AuditBundle\Entity\AuditFormField $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return WG\AuditBundle\Entity\AuditFormField
     */
    public function getField()
    {
        return $this->field;
    }
}