<?php

namespace WG\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use WG\AuditBundle\Entity\AuditScore;
use WG\AuditBundle\Entity\AuditFormField;

class AuditFormFieldType extends AbstractType
{
    const SCORE_YES = 'answer_yes';
    const SCORE_NO = 'answer_no';
    const SCORE_ACCEPTABLE = 'answer_acceptable';
    const SCORE_NOT_APPLICABLE = 'answer_not_applicable';
    
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
        $builder->add( self::SCORE_YES, 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => isset( $scores['Y'] ) ? $scores['Y'] : '',
        ));
        $builder->add( self::SCORE_NO, 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => isset( $scores['N'] ) ? $scores['N'] : '',
        ));
        $builder->add( self::SCORE_ACCEPTABLE, 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => isset( $scores['A'] ) ? $scores['A'] : '',
        ));
        $builder->add( self::SCORE_NOT_APPLICABLE, 'textarea', array(
            'mapped' => false,
            'required' => false,
            'data' => isset( $scores['N/A'] ) ? $scores['N/A'] : '',
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
    
    /**
     * Convenience method for setting a non-mapped field from the form data
     * 
     * @param WG\AuditBundle\Entity\AuditFormField $entity
     * @param array $values
     */
    static public function mapScores( AuditFormField $entity, $values )
    {
        $extraFields = array(
            AuditScore::YES => self::SCORE_YES,
            AuditScore::NO => self::SCORE_NO,
            AuditScore::ACCEPTABLE => self::SCORE_ACCEPTABLE,
            AuditScore::NOT_APPLICABLE => self::SCORE_NOT_APPLICABLE,
        );
        foreach ( $extraFields as $key => $extraField )
        {
            if ( isset( $values[ $extraField ] ) && $values[ $extraField ] )
            {
                $entity->addScore( $key, $values[ $extraField ] );
            }
        }
    }
}
