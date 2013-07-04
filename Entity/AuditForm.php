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
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="string")
     */
    protected $flagLabel;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\AuditFormSection", mappedBy="auditForm")
     */
    protected $sections;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Audit", mappedBy="auditForm")
     */
    protected $audits;

    /**
     * @ORM\OneToOne(targetEntity="CiscoSystems\AuditBundle\Model\MetadataInterface", mappedBy="form")
     */
    protected $metadata;

    public function __construct()
    {
        $this->active = TRUE;
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
     * @return AuditForm
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
     * @return AuditForm
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
     * Set active
     *
     * @param boolean $active
     * @return AuditForm
     */
    public function setActive( $active )
    {
        $this->active = $active;

        return $this;
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
     * @return \CiscoSystems\AuditBundle\Entity\Audit
     */
    public function setFlagLabel( $flagLabel )
    {
        $this->flagLabel = $flagLabel;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AuditForm
     */
    public function setCreatedAt( $createdAt )
    {
        $this->createdAt = $createdAt;

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
     * Set sections
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sections
     */
    public function setSections( \Doctrine\Common\Collections\ArrayCollection $sections = NULL )
    {
        $this->sections = $sections;
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
     * Add a section
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     */
    public function addSection( \CiscoSystems\AuditBundle\Entity\AuditFormSection $section )
    {
        if( !$this->sections->contains( $section ))
        {
            $section->setAuditform( $this );
            $this->sections->add( $section );

            return TRUE;
        }

        return FALSE;
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
            $rem->setAuditForm( NULL );
            $this->sections->removeElement($section);

            return TRUE;
        }

        return FALSE;
    }

    public function removeAllSection()
    {
        foreach( $this->sections as $section )
        {
            $this->removeSection( $section );
        }
    }

    /**
     * Set audits
     *
     * @param \Doctrine\Common\Collections\Collection $audits
     */
    public function setAudits( \Doctrine\Common\Collections\Collection $audits = NULL )
    {
        $this->audits = $audits;
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
     */
    public function addAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( !$this->audits->contains( $audit ))
        {
            $audit->setAuditForm( $this );
            $this->audits->add( $audit );

            return TRUE;
        }

        return FALSE;
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
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     */
    public function removeAudit( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        if( $this->audits->contains( $audit ))
        {
            $index = $this->audits->indexOf( $audit );
            $rem = $this->audits->get( $index );
            $rem->setAuditForm( NULL );
            $this->audits->removeElement( $audit );

            return TRUE;
        }

        return FALSE;
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
     * Set metadata
     *
     * @param \CiscoSystems\AuditBundle\Model\MetadataInterface $metadata
     */
    public function setMetadata( \CiscoSystems\AuditBundle\Model\MetadataInterface $metadata = NULL )
    {
        $this->metadata = $metadata;
    }

    /**
     * Get metadata
     *
     * @return \CiscoSystems\AuditBundle\Model\MetadataInterface
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