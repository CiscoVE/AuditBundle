<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\Element;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\SectionRepository")
 * @ORM\Table(name="cisco_audit__section")
 */
class Section extends Element
{
    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="sections")
     * @ORM\JoinColumn(name="form_id",referencedColumnName="id")
     */
    protected $form;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Field", mappedBy="section")
     */
    protected $fields;

    protected $flag = FALSE;

    public function __construct()
    {
        parent::__construct();
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
            // The following was in place to restrict the weight for non flagged field
            if( !$field->getFlag() == TRUE )
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
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set Flag
     *
     * @param boolean $flag
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get auditForm
     *
     * @return CiscoSystems\AuditBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set auditForm
     *
     * @param string $form
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function setForm( \CiscoSystems\AuditBundle\Entity\Form $form = NULL )
    {
        $this->form = $form;

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
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get \Doctrine\Common\Collections\ArrayCollection
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set fields
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fields
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function setFields( \Doctrine\Common\Collections\ArrayCollection $fields = NULL )
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Add a field to ArrayCollection $this->fields
     *
     * @param CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function addField( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        if( !$this->fields->contains( $field ))
        {
            $field->setSection( $this );
            $this->fields->add( $field );

            return $this;
        }

        return FALSE;
    }

    /**
     * Add all field in ArrayColleciton fields to ArrayCollection $this->fields
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fields
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function addFields( \Doctrine\Common\Collections\ArrayCollection $fields )
    {
        foreach( $fields as $field )
        {
            $this->addField( $field );
        }

        return $this;
    }

    /**
     * Remove Field from ArrayCollection fields
     *
     * @param CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function removeField( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        if( $this->fields->contains( $field ))
        {
            $index = $this->fields->indexOf( $field );
            $rem = $this->fields->get( $index );
            $rem->setSection( NULL );
            $this->fields->removeElement( $field );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove all Field from ArrayCollection fields
     *
     * @return CiscoSystems\AuditBundle\Entity\Section
     */
    public function removeAllField()
    {
        foreach( $this->fields as $field )
        {
            $this->removeField( $field );
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}