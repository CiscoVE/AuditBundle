<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SectionsType extends AbstractType
{
    private $repository;

    public function __construct( ObjectManager $objectManager )
    {
        $this->repository = $objectManager->getRepository( 'CiscoSystemsAuditBundle:Section' );
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $type = $this;
        $choiceList = function( Options $options ) use ( $type )
        {
            return new SimpleChoiceList( $type->getSections( $options['auditform']) );
        };

        $resolver->setDefaults( array(
            'choice_list'   => $choiceList,
            'virtual'       => true,
            'empty_value'   => '(Select Section)',
            'empty_data'    => null,
            'auditform'     => null,
        ));
    }

    public function getSections( $form = null )
    {
        return $this->repository->getSectionOptions( $form );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'audit_section';
    }
}