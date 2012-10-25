<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WG\AuditBundle\Entity\Repository\AuditFormSectionRepository")
 * @ORM\Table(name="wgauditformsection")
 */
class AuditFormSection
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="WG\AuditBundle\Entity\AuditForm")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $auditform;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

    /**
     * @ORM\OneToMany(targetEntity="WG\AuditBundle\Entity\AuditFormField", mappedBy="section")
     */
    protected $fields;

    /**
     * @var integer
     */
    protected $weightPercentage;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->weightPercentage = 0;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        $weight = 0;
        foreach ( $this->fields as $field ) $weight += $field->getWeight();
        return $weight;
    }

    /**
     * Get weightPercentage
     *
     * @return integer
     */
    public function getWeightPercentage()
    {
        return $this->weightPercentage;
    }

    /**
     * Set weightPercentage
     *
     * @param integer $weightPercentage
     */
    public function setWeightPercentage( $weightPercentage )
    {
        $this->weightPercentage = $weightPercentage;
    }

    /**
     * add weight and weight's percentage to current variable
     * $weight and $weightpercentage
     *
     * @param integer $weight
     * @param integer $weightPercentage
     */
    public function addScore( $weight, $weightPercentage )  // deprecated
    {
        $divisor = $this->weight + $weight;
        $this->weightPercentage = $this->weightPercentage * $this->weight / $divisor + $weightPercentage * $weight / $divisor;
        $this->weight += $weight;
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
     * Set auditform
     *
     * @param string $auditform
     * @return AuditFormSection
     */
    public function setAuditform( $auditform )
    {
        $this->auditform = $auditform;

        return $this;
    }

    /**
     * Get auditform
     *
     * @return string
     */
    public function getAuditform()
    {
        return $this->auditform;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AuditFormSection
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
     * @return AuditFormSection
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
     * Set position
     *
     * @param integer $position
     * @return AuditFormSection
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
     * Get fields
     *
     * @return WG\AuditBundle\Entity\AuditFormField $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add a field
     *
     * @param WG\AuditBundle\Entity\AuditFormField $field
     * @return AuditFormSection
     */
    public function addField( AuditFormField $field )
    {
        $field->setSection( $this );
        $this->fields[ ] = $field;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param WG\AuditBundle\Entity\AuditFormField $fields
     */
    public function removeField( AuditFormField $fields )
    {
        $this->fields->removeElement( $fields );
    }

    public function __toString()
    {
        return $this->title;
    }
}