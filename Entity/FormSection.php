<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use CiscoSystems\AuditBundle\Entity\Relation;

/**
 * @ORM\Table(name="audit__form_section")
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\FormSectionRepository")
 */
class FormSection extends Relation
{
    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="sectionRelations")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=true)
     */
    protected $form;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="formRelations")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    protected $section;

    public function __construct( $form = NULL, $section = NULL, $archived = FALSE )
    {
        parent::__construct( $archived );
        $this->form = $form;
        $this->section = $section;
    }

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