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
            'attr'      => array(
                'placeholder'   => 'Title for this form',
                'title'         => 'The field doesn\'t have to be unique but it is highly recommended never the less.',
            ),
        ));
        $builder->add( 'description', 'textarea', array(
            'attr'      => array(
                'placeholder'   => 'Description for the field. This should be as clear as possible'
            ),
        ));
        $builder->add( 'active', 'checkbox', array(
            'label'     => 'Is the form active?',
            'required'  => false,
        ));
        $builder->add( 'flagLabel', 'text', array(
            'label'     => 'Wording for trigger',
            'required'  => false,
            'attr'      => array(
                'placeholder'   => 'Warning / Failed.',
                'title'         => 'This has only to be specified if you have flagged questions. (see field editing)',
            ),
        ));
        $builder->add( 'binaryFlagOnly', 'checkbox', array(
            'label'     => 'Are multiple answer allowed on flagged question?',
            'required'  => false,
            'attr'      => array(
                'title'         => 'By selecting this option you will allow flagged questions to have more than YES and No as answer.',
            ),
        ));
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'CiscoSystems\AuditBundle\Entity\AuditForm',
        ));
    }
}
