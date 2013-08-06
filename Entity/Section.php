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

    public function __construct( $title = null, $description = null )
    {
        parent::__construct( $title, $description );
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

    /**
     * get all fields
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getFields( $archived = NULL )
    {
        $fields = array();
        foreach( $this->fieldRelations as $relation )
        {
            if( NULL === $archived )
            {
                $fields[] = $relation->getField();
            }
            elseif( $archived === $relation->getArchived() )
            {
                $fields[] = $relation->getField();
            }

        }

        return $fields;
    }

    public function addField( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        if( FALSE === array_search( $field, $this->getFields() ))
        {
            $this->addFieldRelation( new SectionField( $this, $field ));

            return $this;
        }

        return FALSE;
    }

    public function removeField( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        if( FALSE !== array_search( $field, $this->getFields() ))
        {
            if( NULL !== $relation = $this->getFieldRelation( $field ))
            {
                $this->removeFieldRelation( $relation );

                return $this;
            }
        }

        return FALSE;
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

    public function getFieldRelation( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        $relation = array_filter(
            $this->fieldRelations->toArray(),
            function( $e ) use ( $field )
            {
                return $e->getField() === $field;
            }
        );

        return reset( $relation );
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

    public function getForms( $archived = NULL )
    {
        $forms = array();
        foreach( $this->formRelations as $relation )
        {
            if( NULL === $archived )
            {
                $forms[] = $relation->getForm();
            }
            elseif( $archived === $relation->getForm() )
            {
                $forms[] = $relation->getForm();
            }
        }

        return $forms;
    }

    /**
     * Add a form
     *
     * @param \CiscoSystems\AuditBundle\Entity\Form $form
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function addForm( \CiscoSystems\AuditBundle\Entity\Form $form )
    {
        if( FALSE === array_search( $form, $this->getForms() ))
        {
            $this->addFormRelation( new FormSection( $form, $this ));

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove a form
     *
     * @param \CiscoSystems\AuditBundle\Entity\Form $form
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function removeForm( \CiscoSystems\AuditBundle\Entity\Form $form )
    {
        if( FALSE !== array_search( $form, $this->getForms() ))
        {
            if( NULL !== $relation = $this->getFormRelation( $form ))
            {
                $this->removeFormRelation( $relation );

                return $this;
            }
        }

        return FALSE;
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

    public function getFormRelation( \CiscoSystems\AuditBundle\Entity\Form $form )
    {
        $relation = array_filter(
            $this->formRelations->toArray(),
            function( $e ) use ( $form )
            {
                return $e->getForm() === $form;
            }
        );

        return reset( $relation );
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