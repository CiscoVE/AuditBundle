<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use WG\AuditBundle\Entity\AuditForm;

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
     * @ORM\Column(type="boolean")
     */
    protected $failed;

    protected $weightPercentage;
    
    protected $weight;
    
    public function getWeightPercentage()
    {
        return $this->weightPercentage;
    }

    public function setWeightPercentage( $weightPercentage )
    {
        $this->weightPercentage = $weightPercentage;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight( $weight )
    {
        $this->weight = $weight;
    }

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;
    
    function __construct()
    {
        $this->weightPercentage = 0;
        $this->weight = 0;
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
}