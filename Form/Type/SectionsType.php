<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
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
//            echo '<div>options: '; print_r( $options['auditform'] ); echo '</div>'; die();
            return new ArrayChoiceList( $type->getSections( $options['form'], $options['archived']) );
        };

        $resolver->setDefaults( array(
            'choice_list'   => $choiceList,
            'virtual'       => TRUE,
            'empty_value'   => '(Select Section)',
            'empty_data'    => NULL,
            'form'          => NULL,
            'archived'      => NULL,
        ));
    }

    public function getSections( $form = NULL, $archived = NULL )
    {
        return $this->repository->getSectionOptions( $form, $archived );
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