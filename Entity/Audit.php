<?php

namespace WG\AuditBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use WG\AuditBundle\Entity\AuditForm;

/**
 * @ORM\Entity
 * @ORM\Table(name="wgaudit")
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
     * @ORM\ManyToOne(targetEntity="WG\AuditBundle\Entity\AuditForm")
     * @ORM\Column(name="audit_form_id")
     */
    protected $auditForm;

    /**
     * @ORM\Column(type="integer", name="audit_reference_id", nullable=true)
     */
    protected $auditReference;

    /**
     * @ORM\Column(type="integer", name="auditing_user_id", nullable=true)
     */
    protected $auditingUser;

    /**
     * @ORM\Column(type="integer", name="control_user_id", nullable=true)
     */
    protected $controlUser;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $failed;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;
}