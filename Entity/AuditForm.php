<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="wgauditform")
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
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     *
     * @ORM\OneToMany(targetEntity="WG\AuditBundle\Entity\AuditFormSection", mappedBy="auditform")
     */
    protected $sections;

    public function __construct()
    {
        $this->active = true;
        $this->sections = new ArrayCollection();
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
     * Add a section
     *
     * @param WG\AuditBundle\Entity\AuditFormSection $section
     * @return AuditForm
     */
    public function addSection(AuditFormSection $section)
    {
        $section->setAuditform( $this );
        $this->sections[] = $section;

        return $this;
    }

    /**
     * Remove sections
     *
     * @param WG\AuditBundle\Entity\AuditFormSection $section
     */
    public function removeSection(AuditFormSection $section)
    {
        if($this->sections->contains( $section ))
        {
            $index = $this->sections->indexOf( $section );
            $rem = $this->sections->get( $index );
            $rem->setAuditForm( null );
        }

        $this->sections->removeElement($section);
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

    public function __toString()
    {
        return $this->title;
    }
}