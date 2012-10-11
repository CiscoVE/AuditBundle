<?php

namespace WG\AuditBundle\Form\Type;

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
        $builder->add( 'title' );
        $builder->add( 'description', 'textarea' );
        $builder->add( 'active', 'checkbox', array(
            'label' => 'Is the form active?',
            'required' => false,
        ) );
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'WG\AuditBundle\Entity\AuditForm',
        ) );
    }

}
