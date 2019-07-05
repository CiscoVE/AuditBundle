<?php

namespace CiscoSystems\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\Element;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\FieldRepository")
 * @ORM\Table(name="audit__field")
 */
class Field extends Element {

    const DEFAULTWEIGHTVALUE = 5;

    /**
     * @var \CiscoSystems\AuditBundle\Entity\AuditSection AuditSection to which the AuditField belongs
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\SectionField", mappedBy="field", cascade="persist")
     */
    protected $sectionRelations;

    /**
     * @var array Array of string values: settable choices
     *
     * @ORM\Column(name="choices",type="array")
     */
    protected $choices;

    /**
     * @var \CiscoSystems\AuditBundle\Entity\Score Score associated with the AuditField
     *
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Score", mappedBy="field")
     */
    protected $scores;

    /**
     * @var integer Weight for the AuditField
     *
     * @ORM\Column(name="weight",type="integer")
     */
    protected $weight;

    /**
     * @var boolean Flag/Trigger for the AuditField
     *
     * @ORM\Column(name="flag",type="boolean")
     */
    protected $flag;

    /**
     * @var boolean Numerical Score for the AuditField
     *
     * @ORM\Column(name="numerical_score",type="boolean")
     */
    protected $numericalScore;

    /**
     * @var boolean Is Wild Card Question for the AuditField
     *
     * @ORM\Column(name="wild_card_question",type="boolean")
     */
    protected $isWildCardQuestion;

    /**
     * @var boolean enabled/diabled AuditField check
     *
     * @ORM\Column(name="disabled",type="boolean")
     */
    protected $disabled;

    public function __construct($title = null, $description = null) {
        parent::__construct($title, $description);
        $this->flag = FALSE;
        $this->auditscores = new ArrayCollection();
        $this->disabled = FALSE;
        $this->weight = self::DEFAULTWEIGHTVALUE;
        $this->sectionRelations = new ArrayCollection();
    }

    /**
     * Get scores
     *
     * @return array
     */
    public function getChoices() {
        return $this->choices;
    }

    /**
     * Set scores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $choices
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setChoices($choices) {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Add score and its label
     *
     * @param string $choice
     * @param string $label
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addChoice($choice, $label) {
        $this->choices[$choice] = $label;

        return $this;
    }

    /**
     * Get auditscore
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getScores() {
        return $this->scores;
    }

    /**
     * Set auditscores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setScores(\Doctrine\Common\Collections\ArrayCollection $scores = NULL) {
        $this->scores = $scores;

        return $this;
    }

    /**
     * Add an auditscore
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addScore(\CiscoSystems\AuditBundle\Entity\Score $score) {
        if (count($this->scores) > 0 && !$this->scores->contains($score)) {
            $this->scores->add($score);
            $score->setField($this);

            return $this;
        }

        return FALSE;
    }

    /**
     * Add auditscores
     *
     * @param array $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function addScores(array $scores) {
        foreach ($scores as $score) {
            $this->addScore($score);
        }

        return $this;
    }

    /**
     * Remove auditscores
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $scores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function removeScore(\CiscoSystems\AuditBundle\Entity\Score $scores) {
        if ($this->scores->contains($scores)) {
            $index = $this->scores->indexOf($scores);
            $rem = $this->scores->get($index);
            $rem->setField(NULL);
            $this->scores->removeElement($scores);

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove all auditscores
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function removeAllScores() {
        foreach ($this->scores as $score) {
            $this->removeScore($score);
        }

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setWeight($weight) {
        $this->weight = ( $weight >= 0 ) ? (is_null($weight) ? 0 : $weight) : self::DEFAULTWEIGHTVALUE;

        return $this;
    }

    /**
     * Get flag
     *
     * @return boolean
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * Set flag
     *
     * @param boolean $flag
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setFlag($flag) {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get numericalScore
     *
     * @return boolean
     */
    public function getNumericalScore()
    {
        return $this->numericalScore;
    }

    /**
     * Set numericalScore
     *
     * @param boolean $numericalScore
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setNumericalScore($numericalScore)
    {
        $this->numericalScore = $numericalScore;

        return $this;
    }

    /**
     * Get isWildCardQuestion
     *
     * @return boolean
     */
    public function getIsWildCardQuestion()
    {
        return $this->isWildCardQuestion;
    }

    /**
     * Set numericawildCardQuestionlScore
     *
     * @param boolean $isWildCardQuestion
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setIsWildCardQuestion($isWildCardQuestion)
    {
        $this->isWildCardQuestion = $isWildCardQuestion;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled() {
        return $this->disabled;
    }

    /**
     * Set disabled
     *
     * @param boolean $boolean
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setDisabled($boolean = FALSE) {
        $this->disabled = $boolean;

        return $this;
    }

    /**
     * Get position for the field and for the given section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return integer|boolean
     */
    public function getPosition(\CiscoSystems\AuditBundle\Entity\Section $section) {
        if (FALSE !== $relation = $this->getSectionRelation($section)) {
            return $relation->getPosition();
        }

        return FALSE;
    }

    /**
     * Get sections, if parameter given (boolean) then only relation
     * section - field with getArchived() === $archived will be returned
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getSections($archived = NULL) {
        $sections = array();
        foreach ($this->sectionRelations as $relation) {
            if (NULL === $archived) {
                $sections[] = $relation->getSection();
            } elseif ($archived === $relation->getArchived()) {
                $sections[] = $relation->getSection();
            }
        }

        return $sections;
    }

    /**
     * get the section for which the relation section-field is NOT archived
     *
     * @return \CiscoSystems\AuditBundle\Entity\Section
     */
    public function getSection() {
        $field = $this;

        $relations = $this->sectionRelations->filter(function( $relation ) use ( $field ) {
            if ($relation->getField() === $field && $relation->getArchived() === FALSE) {
                return $relation;
            }
        });

        return $relations->count() > 0 ? $relations->first()->getSection() : NULL;
    }

    /**
     * Add a section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field|boolean
     */
    public function addSection(\CiscoSystems\AuditBundle\Entity\Section $section) {
        if (FALSE === array_search($section, $this->getSections())) {
            $this->addSectionRelation(new SectionField($section, $this));

            return $this;
        } elseif (TRUE === $this->getSectionRelation($section)->getArchived()) {
            $this->getSectionRelation($section)->setArchived(FALSE);

            return $this;
        }

        return FALSE;
    }

    public function addSections($sections) {
        foreach ($sections as $section) {
            $this->addSection($section);
        }
    }

    /**
     * Remove a Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field|boolean
     */
    public function removeSection(\CiscoSystems\AuditBundle\Entity\Section $section) {
        if (FALSE !== array_search($section, $this->getSections())) {
            if (NULL !== $relation = $this->getSectionRelation($section)) {
                $this->removeSectionRelation($relation);

                return $this;
            }
        }

        return FALSE;
    }

    /**
     * Get collection of relation section - field
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSectionRelations() {
        return $this->sectionRelations;
    }

    /**
     * Set the colleciton of relation section - field
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $relations
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field
     */
    public function setSectionRelations(ArrayCollection $relations) {
        $this->sectionRelations = $relations;

        return $this;
    }

    /**
     * Get a single relation Section - Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return \CiscoSystems\AuditBundle\Entity\SectionField
     */
    public function getSectionRelation(\CiscoSystems\AuditBundle\Entity\Section $section) {
        $relation = array_filter(
                $this->sectionRelations->toArray(), function( $e ) use ( $section ) {
            return $e->getSection() === $section;
        }
        );

        return reset($relation);
    }

    /**
     * Add a relationship Section - Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\SectionField $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field|boolean
     */
    public function addSectionRelation(\CiscoSystems\AuditBundle\Entity\SectionField $relation) {
        if (!$this->sectionRelations->contains($relation)) {
            $this->sectionRelations->add($relation);

            return $this;
        }

        return FALSE;
    }

    /**
     * Remove a relationship Section - Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\SectionField $relation
     *
     * @return \CiscoSystems\AuditBundle\Entity\Field|boolean
     */
    public function removeSectionRelation(\CiscoSystems\AuditBundle\Entity\SectionField $relation) {
        if ($this->sectionRelations->contains($relation)) {
            $relation->setArchived(TRUE);

            return $this;
        }

        return FALSE;
    }

    /**
     * see http://stackoverflow.com/questions/9088603/symfony2-doctrine-how-to-re-save-an-entity-with-a-onetomany-as-a-cascading-new
     */
    public function cloneSectionRelation() {
        $relations = $this->sectionRelations;
        $this->sectionRelations = new ArrayCollection();
        foreach ($relations as $relation) {
            $clone = clone $relation->getSection();
            $this->sectionRelations->add(new SectionField($clone, $this));
            $clone->setField($this);
        }
    }

    public function cloneScores() {
        $scores = $this->scores;
        $this->scores = new ArrayCollection();
        foreach ($scores as $score) {
            $clone = clone $score;
            $this->scores->add($clone);
            $clone->setField($this);
        }
    }

    public function compare(Field $field) {
        $ret = array();

        if ($field->getTitle() !== $this->title) {
            $ret['title'] = array($field->getTitle(), $this->title);
        }
        if ($field->getDescription() !== $this->description) {
            $ret['description'] = array($field->getDescription(), $this->description);
        }
        if ($field->getFlag() !== $this->flag) {
            $ret['flag'] = array($field->getFlag(), $this->flag);
        }
        if ($field->getChoices() !== $this->choices) {
            $ret['choices'] = array($field->getChoices(), $this->choices);
        }
        if ($field->getWeight() !== $this->weight) {
            $ret['weight'] = array($field->getWeight(), $this->weight);
        }

        return ( count($ret) > 0 ) ? $ret : NULL;
    }

    public function isArchived() {
        return $this->getSection() > $this->getSections(TRUE) ?
                FALSE :
                TRUE;
    }

}
