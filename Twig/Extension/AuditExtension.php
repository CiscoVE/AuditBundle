<?php

namespace CiscoSystems\AuditBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use CiscoSystems\AuditBundle\Worker\AuditScoring;

class AuditExtension extends Twig_Extension
{
    protected $scoring;

    public function __construct( AuditScoring $scoring )
    {
        $this->scoring = $scoring;
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
}
