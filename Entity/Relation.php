<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit__relation")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type",type="string")
 * @ORM\DiscriminatorMap({"formsection"="FormSection","sectionfield"="SectionField"})
 */
abstract class Relation
{
    const TYPE_FORMSECTION = 'formsection';
    const TYPE_SECTIONFIELD = 'sectionfield';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="archived",type="boolean")
     */
    protected $archived;

//    protected $form;
//
//    protected $section;
//
//    protected $field;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

    public function __construct( $archived = FALSE )
    {
        $this->archived = $archived;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getArchived()
    {
        return $this->archived;
    }

    public function setArchived( $archived )
    {
        $this->archived = $archived;

        return $this;
    }

    public function getPosition()
    {
        return $this->position + 1;
    }

    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    public function getType()
    {
        return 'relation';
    }

    public function __toString()
    {
        return $this->id . ' ' . $this->getType();
    }
}