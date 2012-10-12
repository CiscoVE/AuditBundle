<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditFormFieldType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $scores = $options['data']->getScores();
        $builder->add( 'id', 'hidden', array( 'mapped' => false ));
        $builder->add( 'section', null, array(
            'empty_data' => '---',
            'required' => true,
        ));
        $builder->add( 'title' );
        $builder->add( 'description', 'textarea' );
        $builder->add( 'weight', 'integer' );
        $builder->add( 'answer_yes', 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => $scores['Y'],
        ));
        $builder->add( 'answer_no', 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => $scores['N'],
        ));
        $builder->add( 'answer_acceptable', 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => $scores['A'],
        ));
        $builder->add( 'answer_not_applicable', 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => $scores['N/A'],
        ));
        $builder->add( 'fatal', 'checkbox', array(
            'label' => 'Is the error fatal?',
            'required' => false,
        ));
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
