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
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\AuditForm", inversedBy="sections")
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
//            The following was in place to restrict the weight for non flagged field
            if( !$field->getFlag() == true )
            {
                $weight += $field->getWeight();
            }
        }
        return $weight;
    }

    /**
     * Get Flag
     *
     * @return boolean
     */
    public function getFlat()
    {
        $flag = false;
        foreach ( $this->fields as $field )
        {
            $flag = ( $field->getFlag() === true  ? true : false );
        }
        return $flag;
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
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormSection
     */
    public function setAuditForm( $auditForm )
    {
        $this->auditForm = $auditForm;

        return $this;
    }

    /**
     * Get auditForm
     *
     * @return CiscoSystems\AuditBundle\Entity\AuditForm
     */
    public function getAuditForm()
    {
        return $this->auditForm;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormSection
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
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormSection
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
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormSection
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
     * Get \Doctrine\Common\Collections\ArrayCollection
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormField
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add a field to ArrayCollection $this->fields
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $field
     * @return \CiscoSystems\AuditBundle\Entity\AuditFormSection
     */
    public function addField( \CiscoSystems\AuditBundle\Entity\AuditFormField $field )
    {
        if( !$this->fields->contains( $field ))
        {
            $field->setSection( $this );
            $this->fields->add( $field );
        }
        return $this;
    }

    /**
     * Add all field in ArrayColleciton fields to ArrayCollection $this->fields
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fields
     */
    public function addFields( \Doctrine\Common\Collections\ArrayCollection $fields )
    {
        foreach( $fields as $field )
        {
            $this->addField( $field );
        }
    }

    /**
     * Remove Field from ArrayCollection fields
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $field
     */
    public function removeField( \CiscoSystems\AuditBundle\Entity\AuditFormField $field )
    {
        if( $this->fields->contains( $field ))
        {
            $index = $this->fields->indexOf( $field );
            $rem = $this->fields->get( $index );
            $this->fields->removeElement( $field );
            $rem->setSection( null );
        }
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