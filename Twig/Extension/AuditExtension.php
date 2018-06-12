<?php

namespace CiscoSystems\AuditBundle\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
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
            new Twig_SimpleFunction('get_resultforsection', array($this, 'getResultForSection')),
            new Twig_SimpleFunction('get_weightforsection', array($this, 'getWeightForSection')),
            new Twig_SimpleFunction('get_resultforaudit', array($this, 'getResultForAudit')),            
            new Twig_SimpleFunction('get_weightforaudit', array($this, 'getWeightForAudit')),
            new Twig_SimpleFunction('get_trigger', array($this, 'getTrigger')),
            new Twig_SimpleFunction('get_relation', array($this, 'getRelation'))
        );
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('position',array($this, 'getPosition')),
            new Twig_SimpleFilter('archived',array($this, 'getArchived')),
            new Twig_SimpleFilter('sections',array($this, 'getSections')),
            new Twig_SimpleFilter('fields',array($this, 'getFields'))
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
