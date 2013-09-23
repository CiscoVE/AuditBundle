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
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\FormSection",mappedBy="section",cascade={"persist"})
     */
    protected $formRelations;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\SectionField",mappedBy="section",cascade={"persist"})
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
     * Get position for the section and for the given form
     *
     * @param \CiscoSystems\AuditBundle\Entity\Form $form
     *
     * @return integer|boolean
     */
    public function getPosition( \CiscoSystems\AuditBundle\Entity\Form $form )
    {
        if( FALSE !== $relation = $this->getFormRelation( $form ) )
        {
            return $relation->getPosition();
        }

        return FALSE;
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

    /**
     * Add a field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function addField( \CiscoSystems\AuditBundle\Entity\Field $field )
    {
        if( FALSE === array_search( $field, $this->getFields() ))
        {
            $this->addFieldRelation( new SectionField( $this, $field ));

            return $this;
        }
        elseif( TRUE === $this->getFieldRelation( $field )->getArchived() )
        {
            $this->getFieldRelation( $field )->setArchived( FALSE );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove a field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
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

    /**
     * Get collection of relation section - field
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFieldRelations()
    {
        return $this->fieldRelations;
    }

    /**
     * Set the colleciton of relation section - field
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $relations
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section
     */
    public function setFieldRelations( ArrayCollection $relations )
    {
        $this->fieldRelations = $relations;

        return $this;
    }

    /**
     * Get a single relation section - field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\SectionField
     */
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

    /**
     * Add a single relation section - field
     *
     * @param \CiscoSystems\AuditBundle\Entity\SectionField $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function addFieldRelation( \CiscoSystems\AuditBundle\Entity\SectionField $relation )
    {
        if( !$this->fieldRelations->contains( $relation ))
        {
            $this->fieldRelations->add( $relation );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove a single relation section - field
     *
     * @param \CiscoSystems\AuditBundle\Entity\SectionField $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function removeFieldRelation( \CiscoSystems\AuditBundle\Entity\SectionField $relation )
    {
        if( $this->fieldRelations->contains( $relation ))
        {
            $relation->setArchived( TRUE );

            return $this;
        }

        return FALSE;
    }

    /**
     * Get forms, if parameter given (boolean) then only relation
     * form - section with getArchived() === $archived will be returned
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getForms( $archived = NULL )
    {
        $forms = array();
        foreach( $this->formRelations as $relation )
        {
            if( NULL === $archived )
            {
                $forms[] = $relation->getForm();
            }
            elseif( $archived === $relation->getArchived() )
            {
                $forms[] = $relation->getForm();
            }
        }

        return $forms;
    }

    public function getForm()
    {
        $section = $this;

        $relations = $this->formRelations->filter( function( $relation ) use ( $section )
        {
            if( $relation->getSection() === $section && $relation->getArchived() === FALSE )
            {
                return $relation;
            }
        });

        return ( $relations->count() > 0 ) ? $relations->first()->getForm() : FALSE ;
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
        elseif( TRUE === $this->getFormRelation( $form )->getArchived() )
        {
            $this->getFormRelation( $form )->setArchived( FALSE );

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

    /**
     * Get a collection of relation form - section
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFormRelations()
    {
        return $this->formRelations;
    }

    /**
     * Set the collection of relation form - section
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $relations
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section
     */
    public function setFormRelations( ArrayCollection $relations )
    {
        $this->formRelations = $relations;

        return $this;
    }

    /**
     * Get a single relation form - section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Form $form
     *
     * @return \CiscoSystems\AuditBundle\Entity\FormSection
     */
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

    /**
     * Add a single relation form - section
     *
     * @param \CiscoSystems\AuditBundle\Entity\FormSection $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function addFormRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( !$this->formRelations->contains( $relation ))
        {
            $this->formRelations->add( $relation );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove a single relation form - section
     *
     * @param \CiscoSystems\AuditBundle\Entity\FormSection $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section|boolean
     */
    public function removeFormRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( $this->formRelations->contains( $relation ))
        {
            $relation->setArchived( TRUE );

            return $this;
        }

        return FALSE;
    }

    public function isArchived()
    {
        return $this->getFields() > $this->getFields( TRUE ) ?
               FALSE :
               TRUE ;
    }
}