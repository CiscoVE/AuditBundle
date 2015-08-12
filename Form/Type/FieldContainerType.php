<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;

class FieldContainerType extends AbstractType
{
    protected $repository;

    public function __construct( ObjectManager $objectManager )
    {
        $this->repository = $objectManager->getRepository( 'CiscoSystemsAuditBundle:Section' );
    }
	
	public function configureOptions( OptionsResolver $resolver )
	{
        $type = $this;
		$resolver->setDefaults( array(
            'choices' => function( Options $options ) use ( $type )
				         {
				             return $type->getSections( $options['form'], $options['archived']);
				         },
   			'choices_as_values' => true,
            'virtual'     => TRUE,
            'placeholder' => '(Select Section)',
            'empty_data'  => NULL,
            'form'        => NULL,
            'archived'    => NULL,
        ));
	}

    public function getSections( $form = NULL, $archived = NULL )
    {
        $options = $this->repository->getSectionOptions( $form, $archived );
        // ...
        return $options;
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
