<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cisco_audit__score")
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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Audit", inversedBy="scores")
     * @ORM\JoinColumn(name="audit_id", referencedColumnName="id")
     */
    protected $audit;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditFormField", inversedBy="auditscores")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;

    /**
     * @ORM\Column(type="string")
     */
    protected $score;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $comment;

    static public function getWeightPercentageForScore( $score )
    {
        switch( $score )
        {
            case AuditScore::YES:
            case AuditScore::NOT_APPLICABLE: return 100;
            case AuditScore::ACCEPTABLE: return 50;
            case AuditScore::NO: break;
        }
        return 0;
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
     *
     * @return AuditScore
     */
    public function setScore( $score )
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
     *
     * @return AuditScore
     */
    public function setComment( $comment )
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
     * @param CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return AuditScore
     */
    public function setAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if ( null == $this->audit ) $this->audit = $audit;

        return $this;
    }

    /**
     * Get audit
     *
     * @return CiscoSystems\AuditBundle\Entity\Audit
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * Set field
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $field
     *
     * @return AuditScore
     */
    public function setField( \CiscoSystems\AuditBundle\Entity\AuditFormField $field = null )
    {
        if ( null == $this->field ) $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return CiscoSystems\AuditBundle\Entity\AuditFormField
     */
    public function getField()
    {
        return $this->field;
    }
}