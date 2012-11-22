<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditFormSectionRepository")
 * @ORM\Table(name="cisco_audit__section")
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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditForm")
     * @ORM\JoinColumn(name="audit_form_id",referencedColumnName="id")
     */
    protected $auditForm;

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
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditFormField", mappedBy="section")
     */
    protected $fields;

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
        foreach ( $this->fields as $field )
        {
            if(!$field->getFatal() == true)
            {
                $weight += $field->getWeight();
            }
        }
        return $weight;
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
     * @return AuditFormSection
     */
    public function setAuditForm( $auditForm )
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
     * @return CiscoSystems\AuditBundle\Entity\AuditFormField $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add a field to ArrayCollection fields
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $field
     * @return AuditFormSection
     */
    public function addField( AuditFormField $field )
    {
        $field->setSection( $this );
        $this->fields[ ] = $field;

        return $this;
    }

    /**
     * Remove Field from ArrayCollection fields
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $field
     */
    public function removeField( AuditFormField $field )
    {
        if( $this->fields->contains( $field ))
        {
            $index = $this->fields->indexOf( $field );
            $rem = $this->fields->get( $index );
            $rem->setSection( null );
        }

        $this->fields->removeElement( $field );
    }
    
    /**
     * Remove all Field from ArrayCollection fields
     */
    public function removeAllField()
    {
        foreach( $this->fields as $field )
        {
            $this->removeField( $field );
        }
    }

    public function __toString()
    {
        return $this->title;
    }
}