<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditFormFieldRepository")
 * @ORM\Table(name="cisco_audit__field")
 */
class Field
{
    const DEFAULTWEIGHTVALUE = 5;

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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="fields")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    protected $section;

    /**
     * @var string Title for the AuditField
     *
     * @ORM\Column(name="title",type="string")
     */
    protected $title;

    /**
     * @var string Description for the AuditField
     *
     * @ORM\Column(name="description",type="string",nullable=true)
     */
    protected $description;

    /**
     * @var array Array of string values: settable choices
     *
     * @ORM\Column(name="choices",type="array")
     */
    protected $choices;

    /**
     * @var \CiscoSystems\AuditBundle\Entity\Score Score associated with the AuditField
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Score", mappedBy="field")
     */
    protected $scores;

    /**
     * @var integer Weight for the AuditField
     *
     * @ORM\Column(name="weight",type="integer")
     */
    protected $weight;

    /**
     * @var boolean Flag/Trigger for the AuditField
     *
     * @ORM\Column(name="flag",type="boolean")
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
     * @ORM\Column(name="slug",length=127, unique=true)
     */
    protected $slug;

    /**
     * @var boolean enabled/diabled AuditField check
     *
     * @ORM\Column(name="disabled",type="boolean")
     */
    protected $disabled;

    public function __construct()
    {
        $this->flag = FALSE;
        $this->auditscores = new ArrayCollection();
        $this->disabled = FALSE;
        $this->weight = self::DEFAULTWEIGHTVALUE;
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setTitle( $title )
    {
        $this->title = $title;

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
     * Set description
     *
     * @param string $description
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setDescription( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get scores
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set scores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $choices
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setChoices( $choices )
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Add score and its label
     *
     * @param string $choice
     * @param string $label
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addChoice( $choice, $label )
    {
        $this->choices[ $choice ] = $label;

        return $this;
    }


    /**
     * Get auditscore
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getScores()
    {
        return $this->scores;
    }
    /**
     * Set auditscores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setScores( \Doctrine\Common\Collections\ArrayCollection $scores = NULL )
    {
        $this->scores = $scores;

        return $this;
    }

    /**
     * Add an auditscore
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addScore( \CiscoSystems\AuditBundle\Entity\Score $score )
    {
        if( count( $this->scores ) > 0 && !$this->scores->contains( $score ))
        {
            $this->scores->add( $score );
            $score->setField( $this );

            return $this;
        }

        return FALSE;
    }

    /**
     * Add auditscores
     *
     * @param array $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addScores( array $scores )
    {
        foreach( $scores as $score )
        {
            $this->addScore( $score );
        }

        return $this;
    }

    /**
     * Remove auditscores
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function removeScore( \CiscoSystems\AuditBundle\Entity\Score $scores )
    {
        if( $this->scores->contains( $scores ) )
        {
            $index = $this->scores->indexOf( $scores );
            $rem = $this->scores->get( $index );
            $rem->setField( NULL );
            $this->scores->removeElement( $scores );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove all auditscores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function removeAllScores()
    {
        foreach ( $this->scores as $score )
        {
            $this->removeScore( $score );
        }

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
     * Set weight
     *
     * @param integer $weight
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setWeight( $weight )
    {
        $this->weight = ( $this->flag === FALSE ) ?
                        (( $weight > 0 ) ? $weight : self::DEFAULTWEIGHTVALUE ) :
                        self::DEFAULTWEIGHTVALUE ;

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
     * Set flag
     *
     * @param boolean $flag
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;

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
     * Set position
     *
     * @param integer $position
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get section
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set section
     *
     * @param CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setSection( \CiscoSystems\AuditBundle\Entity\Section $section = NULL )
    {
        $this->section = $section;

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
     * Set slug
     *
     * @param string $slug
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setSlug( $slug )
    {
        $this->slug = $slug;

        return $this;
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

    /**
     * Set disabled
     *
     * @param boolean $boolean
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setDisabled( $boolean = FALSE )
    {
        $this->disabled = $boolean;

        return $this;
    }
}