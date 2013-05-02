<?php

namespace CiscoSystems\AuditBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use CiscoSystems\AuditBundle\Worker\Score;

class AuditExtension extends Twig_Extension
{
    protected $scoring;

    public function __construct( Score $scoring )
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
            'get_resultforform'     => new Twig_Function_Method( $this, 'getResultForForm' ),
            'get_weightforform'     => new Twig_Function_Method( $this, 'getWeightForForm' ),
        );
    }

    public function getResultForSection( $audit, $section )
    {
        return $this->scoring->getResultForSection( $audit, $section );
    }

    public function getResultForForm( $audit )
    {
        return $this->scoring->getTotalResult( $audit );
    }

    public function getWeightForForm( $audit )
    {
        return $this->scoring->getTotalWeight( $audit );
    }
}
