<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditFormFieldRepository")
 * @ORM\Table(name="cisco_audit__field")
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
     * @ORM\ManyToOne(targetEntity="AuditFormSection")
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
     * array of string values: settable scores
     * @ORM\Column(type="array")
     */
    protected $scores;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditScore", mappedBy="field")
     */
    protected $auditscores;

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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=127, unique=true)
     */
    protected $slug;

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
     * Add score and its label
     *
     * @param string $score
     * @param string $label
     * @return AuditField
     */
    public function addScore( $score, $label )
    {
        $this->scores[$score] = $label;

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
     * Get auditscore
     * 
     * @return type
     */
    public function getAuditscores()
    {
        return $this->auditscores;
    }

    /**
     * Add an auditscore
     * 
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     * @return AuditFormField
     */
    public function addAuditScore( AuditScore $score )
    {
        $score->setField( $this );
        $this->auditscores[] = $score;
        
        return $this;
    }
    
    /**
     * Remove auditscores
     * 
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     */
    public function removeAuditScore( AuditScore $score )
    {
        if($this->auditscores->contains( $score ))
        {
            $index = $this->auditscores->indexOf( $score );
            $rem = $this->auditscores->get( $index );
            $rem->setField( null );
        }
        
        $this->auditscores->removeElement( $score );
    }
    
    /**
     * Remove all auditscores
     */
    public function removeAllAuditScore()
    {
        foreach( $this->auditscores as $auditscore )
        {
            $this->removeAuditScore( $auditscore );
        }
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
     * @param CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     * @return AuditFormField
     */
    public function setSection(\CiscoSystems\AuditBundle\Entity\AuditFormSection $section = null)
    {
        $this->section = $section;
        
        return $this;
    }

    /**
     * Get section
     *
     * @return CiscoSystems\AuditBundle\Entity\AuditFormSection
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return AuditFormField
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}