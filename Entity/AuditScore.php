<?php

namespace WG\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wgauditscore")
 */
class AuditScore
{
    const YES = "Y";
    const NO  = "N";
    const ACCEPTABLE = "A";
    const NOT_APPLICABLE = "N/A";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="WG\AuditBundle\Entity\Audit")
     */
    protected $audit;

    /**
     * @ORM\ManyToOne(targetEntity="WG\AuditBundle\Entity\AuditField")
     */
    protected $field;

    /**
     * @ORM\Column(type="string")
     */
    protected $score;
    
    /**
     * @ORM\Column(type="string",nullable=true)
     */    
    protected $comment;
}