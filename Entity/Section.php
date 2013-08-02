<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\Element;
use CiscoSystems\AuditBundle\Entity\Relation;
use CiscoSystems\AuditBundle\Entity\FormSection;
use CiscoSystems\AuditBundle\Entity\SectionField;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\SectionRepository")
 * @ORM\Table(name="audit__section")
 */
class Section extends Element
{
    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\FormSection", mappedBy="section")
     */
    protected $formRelations;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\SectionField", mappedBy="section")
     */
    protected $fieldRelations;

    protected $flag = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->fieldRelations = new ArrayCollection();
        $this->formRelations = new ArrayCollection();
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
        foreach ( $this->getFields() as $field )
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

    public function getFields()
    {
        $fields = array();
        foreach( $this->fieldRelations as $relation )
        {
            $fields[] = $relation->getField();
        }
        return $fields;
    }

    public function getFieldRelations()
    {
        return $this->fieldRelations;
    }

    public function setFieldRelations( ArrayCollection $relations )
    {
        $this->fieldRelations = $relations;

        return $this;
    }

    public function addFieldRelation( \CiscoSystems\AuditBundle\Entity\SectionField $relation )
    {
        if( !$this->fieldRelations->contains( $relation ))
        {
            $this->fieldRelations->add( $relation );

            return $this;
        }

        return FALSE;
    }

    public function removeFieldRelation( \CiscoSystems\AuditBundle\Entity\SectionField $relation )
    {
        if( $this->fieldRelations->contains( $relation ))
        {
            $relation->setArchived( TRUE );

            return $this;
        }

        return FALSE;
    }

    public function getForms()
    {
        $forms = array();
        foreach( $this->formRelations as $relation )
        {
            $forms[] = $relation->getForm();
        }
        return $forms;
    }

    public function getFormRelations()
    {
        return $this->formRelations;
    }

    public function setFormRelations( ArrayCollection $relations )
    {
        $this->formRelations = $relations;

        return $this;
    }

    public function addFormRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( !$this->formRelations->contains( $relation ))
        {
            $this->formRelations->add( $relation );

            return $this;
        }

        return FALSE;
    }

    public function removeFormRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( $this->formRelations->contains( $relation ))
        {
            $relation->setArchived( TRUE );

            return $this;
        }

        return FALSE;
    }
}