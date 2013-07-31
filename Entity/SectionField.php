<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CiscoSystems\AuditBundle\Entity\Relation;

/**
 * @ORM\Table(name="cisco_audit__section_field")
 * @ORM\Entity
 */
class SectionField extends Relation
{
    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Field", inversedBy="sectionRelations")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=true)
     */
    private $field;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Section", inversedBy="fieldRelations")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     */
    private $section;

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

//CREATE TABLE cisco_audit__relation (id INT AUTO_INCREMENT NOT NULL, archived TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
//CREATE TABLE cisco_audit__section_field (id INT NOT NULL, field_id INT DEFAULT NULL, section_id INT DEFAULT NULL, INDEX IDX_E6C13D4A443707B0 (field_id), INDEX IDX_E6C13D4AD823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
//CREATE TABLE cisco_audit__form_section (id INT NOT NULL, form_id INT DEFAULT NULL, section_id INT DEFAULT NULL, INDEX IDX_102BF82F5FF69B7D (form_id), INDEX IDX_102BF82FD823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4A443707B0 FOREIGN KEY (field_id) REFERENCES cisco_audit__section_field (id);
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4AD823E37A FOREIGN KEY (section_id) REFERENCES cisco_audit__section_field (id);
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4ABF396750 FOREIGN KEY (id) REFERENCES cisco_audit__relation (id) ON DELETE CASCADE;
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82F5FF69B7D FOREIGN KEY (form_id) REFERENCES cisco_audit__form_section (id);
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82FD823E37A FOREIGN KEY (section_id) REFERENCES cisco_audit__form_section (id);
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82FBF396750 FOREIGN KEY (id) REFERENCES cisco_audit__relation (id) ON DELETE CASCADE;
//ALTER TABLE cisco_audit__field DROP FOREIGN KEY cisco_audit__field_ibfk_2;
//DROP INDEX IDX_A09DBB36D823E37A ON cisco_audit__field;
//ALTER TABLE cisco_audit__field DROP section_id;
//ALTER TABLE cisco_audit__section DROP FOREIGN KEY cisco_audit__section_ibfk_2;

//ALTER TABLE cisco_audit__section_field DROP FOREIGN KEY FK_E6C13D4A443707B0;
//ALTER TABLE cisco_audit__section_field DROP FOREIGN KEY FK_E6C13D4AD823E37A;
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4A443707B0 FOREIGN KEY (field_id) REFERENCES cisco_audit__field (id);
//ALTER TABLE cisco_audit__section_field ADD CONSTRAINT FK_E6C13D4AD823E37A FOREIGN KEY (section_id) REFERENCES cisco_audit__section (id);

//ALTER TABLE cisco_audit__form_section DROP FOREIGN KEY FK_102BF82F5FF69B7D;
//ALTER TABLE cisco_audit__form_section DROP FOREIGN KEY FK_102BF82FD823E37A;
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82F5FF69B7D FOREIGN KEY (form_id) REFERENCES cisco_audit__form (id);
//ALTER TABLE cisco_audit__form_section ADD CONSTRAINT FK_102BF82FD823E37A FOREIGN KEY (section_id) REFERENCES cisco_audit__section (id);
