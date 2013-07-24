<?php

namespace CiscoSystems\AuditBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Doctrine\Common\Persistence\ObjectManager;
use CiscoSystems\AuditBundle\Worker\AuditScoring;

class AuditExtension extends Twig_Extension
{
    protected $scoring;
    protected $objectManager;

    public function __construct( AuditScoring $scoring, ObjectManager $objectManager )
    {
        $this->scoring = $scoring;
        $this->objectManager = $objectManager;
    }

    public function getName()
    {
        return 'audit_extension';
    }

    public function getFunctions()
    {
        return array(
            'get_resultforsection'  => new Twig_Function_Method( $this, 'getResultForSection' ),
            'get_weightforsection'  => new Twig_Function_Method( $this, 'getWeightForSection' ),
            'get_resultforaudit'    => new Twig_Function_Method( $this, 'getResultForAudit' ),
            'get_weightforaudit'    => new Twig_Function_Method( $this, 'getWeightForAudit' ),
            'get_trigger'           => new Twig_Function_Method( $this, 'getTrigger' ),
        );
    }

    public function getResultForSection( $audit, $section )
    {
        return $this->scoring->getResultForSection( $audit, $section );
    }

    public function getWeightForSection( $section )
    {
        return $this->scoring->getWeightForSection( $section );
    }

    public function getResultForAudit( $audit )
    {
        return $this->scoring->getResultForAudit( $audit );
    }

    public function getWeightForAudit( $audit )
    {
        return $this->scoring->getWeightForAudit( $audit );
    }

    public function getTrigger( $field )
    {
        $repo = $this->objectManager->getRepository( 'CiscoSystemsAuditBundle:Field' );

        return $repo->getTrigger( $field );
    }
}
