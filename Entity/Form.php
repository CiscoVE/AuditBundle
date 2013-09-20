<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Model\MetadataInterface;
use CiscoSystems\AuditBundle\Entity\Element;
use CiscoSystems\AuditBundle\Entity\FormSection;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\FormRepository")
 * @ORM\Table(name="audit__form")
 */
class Form extends Element
{
    /**
     * @var boolean Is the form active
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @var string Label for the trigger flag
     *
     * @ORM\Column(type="string",name="flag_label")
     */
    protected $flagLabel;

    /**
     * @var boolean Are multiple answer allowed on flagged questions
     *
     * @ORM\Column(type="boolean",name="allow_multi_answer")
     */
    protected $allowMultipleAnswer;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var DoctrineCommon\Collections\ArrayCollection sections that belong to this form
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\FormSection",mappedBy="form",cascade={"persist"})
     */
    protected $sectionRelations;

    /**
     * @var DoctrineCommon\Collections\ArrayCollection audits that are using this form
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Audit",mappedBy="form")
     */
    protected $audits;

    /**
     * @var CiscoSystems\AuditBundle\Model\MetadataInterface metadata for this form
     *
     * @ORM\OneToOne(targetEntity="CiscoSystems\AuditBundle\Model\MetadataInterface",mappedBy="form")
     */
    protected $metadata;

    public function __construct( $title = null, $description = null )
    {
        parent::__construct( $title, $description );
        $this->active = TRUE;
        $this->allowMultipleAnswer = FALSE;
        $this->sectionRelations = new ArrayCollection();
        $this->audits = new ArrayCollection();
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setActive( $boolean )
    {
        $this->active = $boolean;

        return $this;
    }

    /**
     * Get flagLabel
     *
     * @return string
     */
    public function getFlagLabel()
    {
        return $this->flagLabel;
    }

    /**
     * Set flagText
     *
     * @param string $flagLabel
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setFlagLabel( $flagLabel )
    {
        $this->flagLabel = $flagLabel;

        return $this;
    }

    /**
     * Get allowMultipleAnswer
     *
     * @return type
     */
    public function getAllowMultipleAnswer()
    {
        return $this->allowMultipleAnswer;
    }

    /**
     * Set allowMultipleAnswer
     *
     * @param boolean $boolean
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setAllowMultipleAnswer( $boolean )
    {
        $this->allowMultipleAnswer = $boolean;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setCreatedAt( $createdAt )
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set audits
     *
     * @param \Doctrine\Common\Collections\Collection $audits
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setAudits( \Doctrine\Common\Collections\Collection $audits = NULL )
    {
        $this->audits = $audits;

        return $this;
    }

    /**
     * Get audits
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAudits()
    {
        return $this->audits;
    }

    /**
     * Add an Audit to ArrayColletion audits
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function addAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( !$this->audits->contains( $audit ))
        {
            $audit->setForm( $this );
            $this->audits->add( $audit );

            return $this;
        }

        return FALSE;
    }

    /*
     * Add all audit in ArrayColleciton audits to ArrayCollection $this->audits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $audits
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function addAudits( \Doctrine\Common\Collections\ArrayCollection $audits )
    {
        foreach( $audits as $audit )
        {
            $this->addAudit( $audit );
        }

        return $this;
    }

    /**
     * Remove Audit from ArrayColletion audits
     *
     * @param CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function removeAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( $this->audits->contains( $audit ))
        {
            $index = $this->audits->indexOf( $audit );
            $rem = $this->audits->get( $index );
            $rem->setForm( NULL );
            $this->audits->removeElement( $audit );

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove all Audit from ArrayColletion audits
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function removeAllAudit()
    {
        foreach ( $this->audits as $audit )
        {
            $this->removeAudit( $audit );
        }

        return $this;
    }

    /**
     * Get metadata
     *
     * @return CiscoSystems\AuditBundle\Model\MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set metadata
     *
     * @param CiscoSystems\AuditBundle\Model\MetadataInterface $metadata
     *
     * @return CiscoSystems\AuditBundle\Entity\Form $this
     */
    public function setMetadata( \CiscoSystems\AuditBundle\Model\MetadataInterface $metadata = NULL )
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get sections, if parameter given ($archived) then only relation
     * section - field with getArchived() === $archived will be returned
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getSections( $archived = NULL )
    {
        $sections = array();
        foreach( $this->sectionRelations as $relation )
        {
            if( NULL === $archived )
            {
                $sections[] = $relation->getSection();
            }
            elseif( $archived === $relation->getArchived() )
            {
                $sections[] = $relation->getSection();
            }
        }

        return $sections;
    }

    /**
     * Now I feel truely dirty T_T
     *
     * @return boolean
     */
    public function isArchived()
    {
        return $this->getSections() > $this->getSections( TRUE ) ?
               FALSE :
               TRUE ;
    }

    /**
     * Add a single Section to the current form. If the section is already
     * assigned but the relation form - section is set to archived = true, then
     * reset the relation to false.
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\Form|boolean
     */
    public function addSection( \CiscoSystems\AuditBundle\Entity\Section $section )
    {
        if( FALSE === array_search( $section, $this->getSections() ))
        {
            $this->addSectionRelation( new FormSection( $this, $section ) );

            return $this;
        }
        else
        {
            if( TRUE === $status = $this->getSectionRelation( $section )->getArchived())
            {
                $this->getSectionRelation( $section )->setArchived( !$status );

                return $this;
            }
        }

        return FALSE;
    }

    /**
     * Remove a section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\Form|boolean
     */
    public function removeSection( \CiscoSystems\AuditBundle\Entity\Section $section )
    {
        if( FALSE !== array_search( $section, $this->getSections() ))
        {
            if( NULL !== $relation = $this->getSectionRelation( $section ))
            {
                $this->removeSectionRelation( $relation );

                return $this;
            }
        }

        return FALSE;
    }

    /**
     * get the relation form-section for the fiven section
     *
     * see http://stackoverflow.com/questions/4166198/find-array-key-in-objects-array-given-an-attribute-value
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\FormSection
     */
    public function getSectionRelation( $section )
    {
        $relation = array_filter(
            $this->sectionRelations->toArray(),
            function( $e ) use ( $section )
            {
                return $e->getSection() === $section;
            }
        );

        return reset( $relation );
    }

    public function getSectionRelations()
    {
        return $this->sectionRelations;
    }

    public function setSectionRelations( ArrayCollection $relations )
    {
        $this->sectionRelations = $relations;

        return $this;
    }

    public function addSectionRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( !$this->sectionRelations->contains( $relation ))
        {
            $this->sectionRelations->add( $relation );

            return $this;
        }

        return FALSE;
    }

    public function removeSectionRelation( \CiscoSystems\AuditBundle\Entity\FormSection $relation )
    {
        if( $this->sectionRelations->contains( $relation ))
        {
            $relation->setArchived( TRUE );

            return $this;
        }

        return FALSE;
    }

    public function sectionExists( $section )
    {
        foreach( $this->sectionRelations as $relation )
        {
            if( $section === $relation->getSection() && $this === $relation->getForm() )
            {
                return $relation;
            }
        }

        return FALSE;

//        $form = $this;
//        return $this->sectionRelations->exists(
//            function( $relation ) use ( $section, $form )
//            {
//                if( $section === $relation->getSection() && $form === $relation->getForm() )
//                {
//                    return $relation;
//                }
//                else
//                {
//                    return FALSE;
//                }
//            }
//        );
    }
}