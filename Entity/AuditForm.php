<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Model\MetadataInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cisco_audit__form")
 */
class AuditForm
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string Title of the auditform
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string Description of the auditform
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected $description;

    /**
     * @var boolean Is the auditform active
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
     * @var DoctrineCommon\Collections\ArrayCollection sections that belong to this auditform
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditFormSection", mappedBy="auditForm")
     */
    protected $sections;

    /**
     * @var DoctrineCommon\Collections\ArrayCollection audits that are using this auditform
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Audit", mappedBy="auditForm")
     */
    protected $audits;

    /**
     * @var CiscoSystems\AuditBundle\Model\MetadataInterface metadata for this auditform
     *
     * @ORM\OneToOne(targetEntity="CiscoSystems\AuditBundle\Model\MetadataInterface", mappedBy="form")
     */
    protected $metadata;

    public function __construct()
    {
        $this->active = TRUE;
        $this->allowMultipleAnswer = FALSE;
        $this->sections = new ArrayCollection();
        $this->audits = new ArrayCollection();
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
     */
    public function setTitle( $title )
    {
        $this->title = $title;
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
     */
    public function setDescription( $description )
    {
        $this->description = $description;
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
     * Set active
     *
     * @param boolean $active
     */
    public function setActive( $boolean )
    {
        $this->active = $boolean;
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
     */
    public function setFlagLabel( $flagLabel )
    {
        $this->flagLabel = $flagLabel;
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
     */
    public function setAllowMultipleAnswer( $boolean )
    {
        $this->allowMultipleAnswer = $boolean;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt( $createdAt )
    {
        $this->createdAt = $createdAt;
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
     * Add a section
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     *
     * @return CiscoSystems\AuditBundle\Entity\AuditForm
     */
    public function addSection( \CiscoSystems\AuditBundle\Entity\AuditFormSection $section )
    {
        if( !$this->sections->contains( $section ))
        {
            $section->setAuditform( $this );
            $this->sections->add( $section );
        }
        return $this;
    }

    /**
     * Add all section in ArrayColleciton sections to ArrayCollection $this->sections
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sections
     */
    public function addSections( \Doctrine\Common\Collections\ArrayCollection $sections )
    {
        foreach( $sections as $section )
        {
            $this->addSection( $section );
        }
    }

    /**
     * Remove sections
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     */
    public function removeSection( \CiscoSystems\AuditBundle\Entity\AuditFormSection $section )
    {
        if( $this->sections->contains( $section ))
        {
            $index = $this->sections->indexOf( $section );
            $rem = $this->sections->get( $index );
            $this->sections->removeElement($section);
            $rem->setAuditForm( null );
        }
    }

    /**
     * Remove all sections
     */
    public function removeAllSection()
    {
        foreach( $this->sections as $section )
        {
            $this->removeSection( $section );
        }
    }

    /**
     * Get sections
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Add an Audit to ArrayColletion audits
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return CiscoSystems\AuditBundle\Entity\AuditForm
     */
    public function addAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( !$this->audits->contains( $audit ))
        {
            $audit->setAuditForm( $this );
            $this->audits->add( $audit );
        }
        return $this;
    }

    /*
     * Add all audit in ArrayColleciton audits to ArrayCollection $this->audits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $audits
     */
    public function addAudits( \Doctrine\Common\Collections\ArrayCollection $audits )
    {
        foreach( $audits as $audit )
        {
            $this->addAudit( $audit );
        }
    }

    /**
     * Remove Audit from ArrayColletion audits
     *
     * @param CiscoSystems\AuditBundle\Entity\Audit $audit
     */
    public function removeAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( $this->audits->contains( $audit ))
        {
            $index = $this->audits->indexOf( $audit );
            $rem = $this->audits->get( $index );
            $this->audits->removeElement( $audit );
            $rem->setAuditForm( NULL );
        }
    }

    /**
     * Remove all Audit from ArrayColletion audits
     */
    public function removeAllAudit()
    {
        foreach ( $this->audits as $audit )
        {
            $this->removeAudit( $audit );
        }
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
     * Set metadata
     *
     * @param CiscoSystems\AuditBundle\Model\MetadataInterface $metadata
     *
     * @return CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setMetadata( \CiscoSystems\AuditBundle\Model\MetadataInterface $metadata )
    {
        $this->metadata = $metadata;

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

    public function __toString()
    {
        return $this->title;
    }
}