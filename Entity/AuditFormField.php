<?php

namespace CiscoSystems\AuditBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToOne(targetEntity="AuditFormSection", inversedBy="fields")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
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
     * @Assert\NotBlank(message="a weight must be provided.")
     * @Assert\Type(type="integer", message="the weight must be an integer.")
     * @Assert\Min(limit="1", message="the value entered must be greater than 0.")
     */
    protected $weight;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $flag;

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

    public function __construct()
    {
        $this->flag = FALSE;
        $this->auditscores = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle( $title )
    {
        $this->title = $title;
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
     */
    public function setDescription( $description )
    {
        $this->description = $description;
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
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     */
    public function setScores( $scores )
    {
        $this->scores = $scores;
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
     * Add score and its label
     *
     * @param string $score
     * @param string $label
     */
    public function addScore( $score, $label )
    {
        $this->scores[ $score ] = $label;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     */
    public function setWeight( $weight )
    {
        $this->weight = $weight;
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
     * Set flag
     *
     * @param boolean $flag
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;
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
     * Set position
     *
     * @param integer $position
     */
    public function setPosition( $position )
    {
        $this->position = $position;
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
     */
    public function setSection( \CiscoSystems\AuditBundle\Entity\AuditFormSection $section = NULL )
    {
        $this->section = $section;
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
     */
    public function setSlug( $slug )
    {
        $this->slug = $slug;
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

    /**
     * Set auditscores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $auditscores
     */
    public function setAuditScores( \Doctrine\Common\Collections\ArrayCollection $auditscores = NULL )
    {
        $this->auditscores = $auditscores;
    }

    /**
     * Get auditscores
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAuditscores()
    {
        return $this->auditscores ;
    }

    /**
     * Add an auditscore
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $auditscore
     */
    public function addAuditScore( \CiscoSystems\AuditBundle\Entity\AuditScore $auditscore )
    {
        if( count( $this->auditscores ) > 0 && !$this->auditscores->contains( $auditscore ))
        {
            $this->auditscores->add( $auditscore );
            $auditscore->setField( $this );

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Add auditscores
     *
     * @param array $auditscores
     */
    public function addAuditScores( array $auditscores )
    {
        foreach( $auditscores as $auditscore )
        {
            $this->addAuditScore( $auditscore );
        }
    }

    /**
     * Remove auditscores
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $auditscore
     */
    public function removeAuditScore( \CiscoSystems\AuditBundle\Entity\AuditScore $auditscore )
    {
        if( $this->auditscores->contains( $auditscore ) )
        {
            $index = $this->auditscores->indexOf( $auditscore );
            $rem = $this->auditscores->get( $index );
            $rem->setField( NULL );
            $this->auditscores->removeElement( $auditscore );

            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * Remove all auditscores
     */
    public function removeAllAuditScore()
    {
        foreach ( $this->auditscores as $auditscore )
        {
            $this->removeAuditScore( $auditscore );
        }
    }
}