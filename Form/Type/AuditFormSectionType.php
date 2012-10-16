<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditFormSectionType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'id', 'hidden', array(
            'mapped' => false
        ));
        $builder->add( 'title', null, array(
            'attr' => array( 'placeholder' => 'title of the section' ),
        ));
        $builder->add( 'auditform', null, array(
            'empty_data' => '---',
            'required' => true,
            'label' => 'Audit',
        ));      
        $builder->add( 'description', 'textarea', array(
            'attr' => array( 'placeholder' => 'description for the section. This should be as clear as possible' ),
        ));
    }

    public function getName()
    {
        return 'section';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'WG\AuditBundle\Entity\AuditFormSection',
        ));
    }
}
