<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditFormType extends AbstractType
{
    public function getName()
    {
        return 'form';
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'id', 'hidden', array(
            'mapped' => false
        ) );
        $builder->add( 'title', null, array(
            'attr' => array( 'placeholder' => 'Title for this form' ),            
        ));
        $builder->add( 'description', 'textarea', array(
            'attr' => array( 'placeholder' => 'Description for the field. This should be as clear as possible' ),
        ));
        $builder->add( 'active', 'checkbox', array(
            'label' => 'Is the form active?',
            'required' => false,
        ));
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'CiscoSystems\AuditBundle\Entity\AuditForm',
        ));
    }
}
