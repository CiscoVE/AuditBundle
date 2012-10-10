<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

class AuditType extends AbstractType
{
    public function getName()
    {
        return 'audit';
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'audit', 'textarea' );
        $builder->add( 'auditFormSection', null, array(
            'class' => 'SalesForceBundle\Entity\AuditFormSection'
        ));
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'WG\AuditBundle\Entity\Audit',
        ));
    }
}
