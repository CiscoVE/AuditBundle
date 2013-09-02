<?php

namespace CiscoSystems\AuditBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Twig_SimpleFilter;
use Doctrine\Common\Persistence\ObjectManager;
use CiscoSystems\AuditBundle\Worker\Scoring;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Field;

class AuditExtension extends Twig_Extension
{
    protected $scoring;
    protected $objectManager;

    public function __construct( Scoring $scoring, ObjectManager $objectManager )
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
            'get_relation'          => new Twig_Function_Method( $this, 'getRelation' ),
        );
    }

    public function getFilters()
    {
        return array(
            'position'              => new \Twig_Filter_Method( $this, 'getPosition' ),
            'archived'              => new \Twig_Filter_Method( $this, 'getArchived' ),
            'sections'              => new \Twig_Filter_Method( $this, 'getSections' ),
            'fields'                => new \Twig_Filter_Method( $this, 'getFields' ),
        );
    }

    public function getResultForSection( $audit, $section )
    {
        return $this->scoring->getResultForSection( $audit, $section );
    }

    public function getWeightForSection( $audit, $section )
    {
        return $this->scoring->getWeightForSection( $audit, $section );
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
        return $this->objectManager
                    ->getRepository( 'CiscoSystemsAuditBundle:Field' )
                    ->getTrigger( $field );
    }

    public function getRelation( $element, $parent )
    {
        if( $element instanceof Section && $parent instanceof Form )
        {
            return $element->getFormRelation( $parent );
        }
        elseif( $element instanceof Field && $parent instanceof Section )
        {
            return $element->getSectionRelation( $parent );
        }
    }

    public function getPosition( $element, $parent )
    {
        return $this->getRelation( $element, $parent )->getPosition();
    }

    public function getArchived( $element, $parent )
    {
        return $this->getRelation( $element, $parent )->getArchived();
    }

    public function getSections( $form, $archived )
    {
        return $form->getSections( $archived );
    }

    public function getFields( $section, $archived )
    {
        return $section->getFields( $archived );
    }
}
