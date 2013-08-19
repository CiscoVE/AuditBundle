<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use CiscoSystems\AuditBundle\Entity\Relation;

/**
 * @ORM\Table(name="audit__section_field")
 * @ORM\Entity
 */
class SectionField extends Relation
{
    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="fieldRelations", cascade={"persist"})
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    protected $section;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Field", inversedBy="sectionRelations", cascade={"persist"})
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=true)
     */
    protected $field;

    public function __construct( $section = NULL, $field = NULL, $archived = FALSE  )
    {
        parent::__construct( $archived );
        $this->field = $field;
        $this->section = $section;
    }

    public function getField()
    {
        return $this->field;
    }

    public function setField( $field )
    {
        $this->field = $field;

        return $this;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function setSection( $section )
    {
        $this->section = $section;

        return $this;
    }

    public function getType()
    {
        return parent::TYPE_SECTIONFIELD;
    }
}
// see http://stackoverflow.com/questions/14947080/doctrine2-many-to-many-with-extra-columns-in-reference-table-add-record
