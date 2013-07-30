<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CiscoSystems\AuditBundle\Entity\Relation;

/**
 * @ORM\Table(name="cisco_audit__form_section")
 * @ORM\Entity
 */
class FormSection extends Relation
{
    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="sections")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=true)
     */
    private $form;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="forms")
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
}