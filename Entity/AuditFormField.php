<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="WG\AuditBundle\Entity\Repository\AuditFormFieldRepository")
 * @ORM\Table(name="wgauditformfield")
 */
class AuditFormField
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="WG\AuditBundle\Entity\AuditFormSection")
     */
    protected $section;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="array")
     */
    protected $scores;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $fatal;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

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
     * Set title
     *
     * @param string $title
     * @return AuditFormField
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AuditFormField
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set scores
     *
     * @param array $scores
     * @return AuditFormField
     */
    public function setScores($scores)
    {
        $this->scores = $scores;
    
        return $this;
    }

    /**
     * Get scores
     *
     * @return array 
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return AuditFormField
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set fatal
     *
     * @param boolean $fatal
     * @return AuditFormField
     */
    public function setFatal($fatal)
    {
        $this->fatal = $fatal;
    
        return $this;
    }

    /**
     * Get fatal
     *
     * @return boolean 
     */
    public function getFatal()
    {
        return $this->fatal;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return AuditFormField
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set section
     *
     * @param WG\AuditBundle\Entity\AuditFormSection $section
     * @return AuditFormField
     */
    public function setSection(\WG\AuditBundle\Entity\AuditFormSection $section = null)
    {
        $this->section = $section;
    
        return $this;
    }

    /**
     * Get section
     *
     * @return WG\AuditBundle\Entity\AuditFormSection 
     */
    public function getSection()
    {
        return $this->section;
    }
}