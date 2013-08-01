<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use CiscoSystems\AuditBundle\Entity\Relation;

/**
 * @ORM\Table(name="audit__form_section")
 * @ORM\Entity
 */
class FormSection extends Relation
{
    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="sectionRelations")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=true)
     */
    private $form;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="formRelations")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    private $section;

    public function getForm()
    {
        return $this->form;
    }

    public function setForm( $form )
    {
        $this->form = $form;

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
        return parent::TYPE_FORMSECTION;
    }
}