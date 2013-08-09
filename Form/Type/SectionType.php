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
            'attr' => array( 'placeholder' => 'Section\'s title',
                'title'         => 'The field doesn\'t have to be unique but it is highly recommended never the less.',
                'class'         => 'input-xxlarge',
                'rows'          => 1,
                'data-toggle'   => 'tooltip',
            ),
        ));
        $builder->add( 'form', 'entity', array(
            'empty_data'    => null,
            'empty_value'   => '(Choose a Form)',
            'required'      => false,
            'class'         => 'CiscoSystemsAuditBundle:Form',
            'property'      => 'title',
            'label'         => 'Form',
            'attr'          => array(
                'class'         => 'input-xlarge',
            ),
        ));
        $builder->add( 'description', 'textarea', array(
            'attr' => array(
                'placeholder'   => 'Section\'s description. This should be as clear as possible',
                'class'         => 'input-xxlarge',
                'rows'          => 5,
            ),
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
