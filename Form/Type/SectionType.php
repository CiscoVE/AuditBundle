<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SectionType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'id', 'hidden', array(
            'mapped' => false
        ));
        $builder->add( 'title', null, array(
            'attr' => array( 'placeholder' => 'Section\'s title ' ),
        ));
//        $builder->add( 'form', 'entity', array(
//            'empty_data'    => null,
//            'empty_value'   => '(Choose a Form)',
//            'required'      => false,
//            'class'         => 'CiscoSystemsAuditBundle:Form',
//            'property'      => 'title',
//            'label'         => 'Audit',
//        ));
        $builder->add( 'description', 'textarea', array(
            'attr' => array( 'placeholder' => 'Section\'s description. This should be as clear as possible' ),
        ));
    }

    public function getName()
    {
        return 'section';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'CiscoSystems\AuditBundle\Entity\Section',
        ));
    }
}
