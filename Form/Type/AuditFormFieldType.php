<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditFormFieldType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'id', 'hidden' );
        $builder->add( 'section', null, array(
            'empty_value' => '---',
            'required' => true,
        ));
        $builder->add( 'title' );
        $builder->add( 'description' );
        $builder->add( 'answer_yes', 'textarea', array(
            'mapped' => false,
            'required' => false,
        ));
        $builder->add( 'answer_no', 'textarea', array(
            'mapped' => false,
            'required' => false,
        ));
        $builder->add( 'answer_acceptable', 'textarea', array(
            'mapped' => false,
            'required' => false,
        ));
        $builder->add( 'answer_not_applicable', 'textarea', array(
            'mapped' => false,
            'required' => false,
        ));
        $builder->add( 'weight', 'integer' );
    }

    public function getName()
    {
        return 'field';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'WG\AuditBundle\Entity\AuditFormField',
        ));
    }
}
