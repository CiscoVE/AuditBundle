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

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position",type="integer")
     */
    protected $position;

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
}

//CREATE TABLE cisco_audit__section_field (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
//CREATE TABLE cisco_audit__form_section (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4ABF396750 FOREIGN KEY (id) REFERENCES cisco_audit__join_element (id) ON DELETE CASCADE;
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82FBF396750 FOREIGN KEY (id) REFERENCES cisco_audit__join_element (id) ON DELETE CASCADE;
//ALTER TABLE cisco_audit__join_element ADD type VARCHAR(255) NOT NULL;
//ALTER TABLE cisco_audit__section DROP FOREIGN KEY cisco_audit__section_ibfk_2;
//ALTER TABLE cisco_audit__audit ADD CONSTRAINT FK_697001171645DEA9 FOREIGN KEY (reference_id) REFERENCES sfdc (id);
