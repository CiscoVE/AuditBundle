<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditScoreType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'audit', 'hidden' );
        $builder->add( 'field', 'hidden' );
        $builder->add( 'score', 'text' );
        $builder->add( 'comment', 'textarea' );
    }

    public function getName()
    {
        return 'score';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'WG\AuditBundle\Entity\AuditScore',
        ) );
    }

}