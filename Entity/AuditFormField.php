<?php

namespace CiscoSystems\AuditBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditFormFieldRepository")
 * @ORM\Table(name="cisco_audit__field")
 */
class AuditFormField
{
    /**
     * @var integer Id for the AuditField
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \CiscoSystems\AuditBundle\Entity\AuditSection AuditSection to which the AuditField belongs
     *
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditFormSection", inversedBy="fields")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    protected $section;

    /**
     * @var string Title for the AuditField
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string Description for the AuditField
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected $description;

    /**
     * @var array Array of string values: settable scores
     *
     * @ORM\Column(type="array")
     */
    protected $scores;

    /**
     * @var \CiscoSystems\AuditBundle\Entity\AuditScore AuditScore associated with the AuditField
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditScore", mappedBy="field")
     */
    protected $auditscores;

    /**
     * @var integer Weight for the AuditField
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="a weight must be provided.")
     * @Assert\Type(type="integer", message="the weight must be an integer.")
     * @Assert\Min(limit="1", message="the value entered must be greater than 0.")
     */
    protected $weight;

    /**
     * @var boolean Flag/Trigger for the AuditField
     *
     * @ORM\Column(type="boolean")
     */
    protected $flag;

    /**
     * @var integer position of the AuditField in the associated AuditSection
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

    /**
     * @var string Slug for the AuditField
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=127, unique=true)
     */
    protected $slug;

    /**
     * @var boolean enabled/diabled AuditField check
     *
     * @ORM\Column(type="boolean")
     */
    protected $disabled;

    public function __construct()
    {
        $this->disabled = FALSE;
        $this->weight = 1;
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
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormField
     */
    public function setTitle( $title )
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
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormField
     */
    public function setDescription( $description )
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
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function setScores( $scores )
    {
        $this->scores = $scores;

        return $this;
    }

    /**
     * Add score and its label
     *
     * @param string $score
     * @param string $label
     *
     * @return AuditField
     */
    public function addScore( $score, $label )
    {
        $this->scores[ $score ] = $label;

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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAuditscores()
    {
        return $this->auditscores;
    }

    /**
     * Add an auditscore
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormField
     */
    public function addAuditScore( \CiscoSystems\AuditBundle\Entity\AuditScore $score )
    {
        if( $this->auditscores->contains( $score ))
        {
            $score->setField( $this );
            $this->auditscores->add( $score );
        }

        return $this;
    }

    /**
     * Remove auditscores
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     */
    public function removeAuditScore( \CiscoSystems\AuditBundle\Entity\AuditScore $score )
    {
        if ( $this->auditscores->contains( $score ) )
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
        foreach ( $this->auditscores as $auditscore )
        {
            $this->removeAuditScore( $auditscore );
        }
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return \CiscoSystems\AuditBundle\Entity\uditFormField
     */
    public function setWeight( $weight )
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
     * Set flag
     *
     * @param boolean $flag
     *
     * @return AuditFormField
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;

        return $this;
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
     *
     * @return AuditFormField
     */
    public function setPosition( $position )
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
     *
     * @return AuditFormField
     */
    public function setSection( \CiscoSystems\AuditBundle\Entity\AuditFormSection $section = null )
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
     *
     * @return AuditFormField
     */
    public function setSlug( $slug )
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

    /**
     * Set disabled
     *
     * @param boolean $boolean
     */
    public function setDisabled( $boolean = FALSE )
    {
        $this->disabled = $boolean;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }
}