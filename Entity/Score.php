<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\ScoreRepository")
 * @ORM\Table(name="cisco_audit__score")
 */
class Score
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
     * @ORM\JoinColumn(name="audit_id",referencedColumnName="id")
     */
    protected $audit;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Field", inversedBy="scores")
     * @ORM\JoinColumn(name="field_id",referencedColumnName="id")
     */
    protected $field;

    /**
     * @ORM\Column(name="mark",type="string",length=10)
     */
    protected $mark;

    /**
     * @var interger
     */
    protected $weightPercentage;

    /**
     * @ORM\Column(name="comment",type="string",nullable=true)
     */
    protected $comment;

    public function getWeightPercentage()
    {
        $this->weightPercentage = self::getWeightPercentageForScore( $this->score );

        return $this->weightPercentage;
    }

    static public function getWeightPercentageForScore( $score )
    {
        switch( $score )
        {
            case Score::YES:
            case Score::NOT_APPLICABLE: return 100;
            case Score::ACCEPTABLE: return 50;
            case Score::NO: break;
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
     * Get score
     *
     * @return string
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set score
     *
     * @param string $mark
     *
     * @return CiscoSystems\AuditBundle\Entity\Score
     */
    public function setMark( $mark )
    {
        $this->mark = $mark;

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
     * Set comment
     *
     * @param string $comment
     *
     * @return CiscoSystems\AuditBundle\Entity\Score
     */
    public function setComment( $comment )
    {
        $this->comment = $comment;

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
     * Set audit
     *
     * @param CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return CiscoSystems\AuditBundle\Entity\Score
     */
    public function setAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( NULL === $this->audit ) $this->audit = $audit;

        return $this;
    }

    /**
     * Get field
     *
     * @return CiscoSystems\AuditBundle\Entity\Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set field
     *
     * @param CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return CiscoSystems\AuditBundle\Entity\Score
     */
    public function setField( \CiscoSystems\AuditBundle\Entity\Field $field = NULL )
    {
        if( NULL === $this->field ) $this->field = $field;

        return $this;
    }
}