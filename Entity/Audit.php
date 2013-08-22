<?php

namespace CiscoSystems\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Score;
use CiscoSystems\AuditBundle\Model\UserInterface;
use CiscoSystems\AuditBundle\Model\ReferenceInterface;

/**
 * @ORM\Entity(repositoryClass="CiscoSystems\AuditBundle\Entity\Repository\AuditRepository")
 * @ORM\Table(name="audit__audit")
 */
class Audit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Entity\Form", inversedBy="audits")
     * @ORM\JoinColumn(name="form_id",referencedColumnName="id")
     */
    protected $form;

    /**
     * TODO: In order to save the state of the form, lets build an array and
     * persist it as it is in the database (as opposed to saving only the form itself)
     *
     * So when calling the audit->getForm(), it would return an array as follow:
     *
     *
     * id => array(
     *      id => array(
     *          id => array( id, id )
     *      )
     * )
     *
     * Form: array(
     *      id: $id, Sections: array(
     *          id: $id, Fields: array( id: $id, ... )
     *      )
     * )
     * ALTER TABLE audit__audit ADD state LONGTEXT NOT NULL COMMENT '(DC2Type:array)';
     *
     */

    /**
     * @var array nested array representing the state of the form at the time of
     * the audit.
     *
     * @ORM\Column(name="form_state",type="array")
     */
    protected $formState;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\ReferenceInterface")
     * @ORM\JoinColumn(name="reference_id",referencedColumnName="id")
     */
    protected $reference;

    /**
     * @ORM\ManyToOne(targetEntity="CiscoSystems\AuditBundle\Model\UserInterface")
     * @ORM\JoinColumn(name="auditor_id",referencedColumnName="id")
     */
    protected $auditor;

    /**
     * @ORM\Column(name="flag",type="boolean")
     */
    protected $flag;

    /**
     * @ORM\Column(name="mark",type="float",nullable=true)
     */
    protected $mark;

    /**
     * @ORM\OneToMany(targetEntity="CiscoSystems\AuditBundle\Entity\Score",mappedBy="audit")
     */
    protected $scores;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    function __construct()
    {
        $this->scores = new ArrayCollection();
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
     * Get total score
     *
     * @return integer
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set total score
     *
     * @param integer $mark
     */
    public function setMark( $mark )
    {
        $this->mark = $mark;
    }

    /**
     * Get auditForm
     *
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set auditForm
     *
     * @param string $form
     */
    public function setForm( \CiscoSystems\AuditBundle\Entity\Form $form = NULL )
    {
        $this->form = $form;
        if( NULL !== $form ) $this->setFormState();

        return $this;
    }

    public function getFormState()
    {
        return $this->formState;
    }

    public function setFormState( array $formState = NULL )
    {
        if( NULL === $formState )
        {
            $sections = array();

            foreach( $this->form->getSections() as $section )
            {
                $fields = array();
                foreach( $section->getFields() as $field )
                {
                    $fields[] = $field->getId();
                }
                $sections[] = array(
                    'id' => $section->getId(),
                    'fields' => $fields
                );
            }

            $this->formState = array(
                'id' => $this->form->getId(),
                'sections' => $sections
            );

//            echo "<div>"; print_r( $this->formState ); echo "</div>"; die();
        }
        else
        {
            $this->formState = $formState;
        }

        return $this;
    }

    /**
     * Get reference
     *
     * @return \CiscoSystems\AuditBundle\Model\ReferenceInterface
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference
     *
     * @param \CiscoSystems\AuditBundle\Model\ReferenceInterface $reference
     */
    public function setReference( \CiscoSystems\AuditBundle\Model\ReferenceInterface $reference )
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get auditingUser
     *
     * @return \CiscoSystems\AuditBundle\Model\UserInterface
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Set auditingUser
     *
     * @param \CiscoSystems\AuditBundle\Model\UserInterface $auditor
     */
    public function setAuditor( \CiscoSystems\AuditBundle\Model\UserInterface $auditor )
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get flag
     *
     * @return boolean
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set flag
     *
     * @param boolean $flag
     */
    public function setFlag( $flag )
    {
        $this->flag = $flag;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt( \DateTime $createdAt )
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get scores
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set scores
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $scores
     */
    public function setScores( \Doctrine\Common\Collections\ArrayCollection $scores = NULL )
    {
        $this->scores = $scores;
    }

    /**
     * Add a score to collection scores and set audit in the score instance
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     */
    public function addScore( \CiscoSystems\AuditBundle\Entity\Score $score )
    {
        if( !$this->scores->contains( $score ) )
        {
            $score->setAudit( $this );
            $this->scores->add( $score );

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Remove score
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     */
    public function removeScore( \CiscoSystems\AuditBundle\Entity\Score $score )
    {
        if( $this->scores->contains( $score ))
        {
            $this->scores->removeElement( $score );

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Remove all score from ArrayCollection $this->scores
     */
    public function removeAllScores()
    {
        foreach( $this->scores as $score )
        {
            $this->removeScore( $score );
        }
    }
}
