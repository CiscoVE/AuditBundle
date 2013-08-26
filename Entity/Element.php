<?php
namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit__element")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type",type="string")
 * @ORM\DiscriminatorMap({"form"="Form","section"="Section","field"="Field"})
 */
abstract class Element
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string title
     *
     * @ORM\Column(name="title",type="string",length=50)
     */
    protected $title;

    /**
     * @var string description
     *
     * @ORM\Column(name="description",type="string",nullable=true)
     */
    protected $description;

    /**
     * @var boolean archived
     *
     * @ORM\Column(name="archived",type="boolean")
     */
    protected $archived;

    public function __construct( $title = NULL, $description = NULL )
    {
        $this->title = $title;
        $this->description = $description;
        $this->archived = FALSE;
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
     * Set id
     *
     * @param int $id
     *
     * @return \CiscoSystems\AuditBundle\Entity\Element
     */
    public function setId( $id )
    {
        $this->id = $id;

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
     * Set title
     *
     * @param string $title
     *
     * @return CiscoSystems\AuditBundle\Entity\Element $this
     */
    public function setTitle( $title )
    {
        $this->title = $title;

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
     * Set description
     *
     * @param string $description
     *
     * @return CiscoSystems\AuditBundle\Entity\Element $this
     */
    public function setDescription( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     *
     * @return CiscoSystems\AuditBundle\Entity\Element $this
     */
    public function setArchived( $archived )
    {
        $this->archived = $archived;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}