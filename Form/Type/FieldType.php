<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CiscoSystems\AuditBundle\Entity\Score;
use CiscoSystems\AuditBundle\Entity\Field;

class FieldType extends AbstractType
{
    const SCORE_YES = 'answer_yes';
    const SCORE_NO = 'answer_no';
    const SCORE_ACCEPTABLE = 'answer_acceptable';
    const SCORE_NOT_APPLICABLE = 'answer_not_applicable';
    const TOOLTIP = 'Always available.';
    const TOOLTIPFLAG = '!! If this is enable, some of the fields below will not be editable.';
    const TOOLTIPOPTIONAL = '!! Only available when the form is allowing for multiple answers and the field is not set to raise a flag.';
    const TOOLTIPWEIGHT = '!! Only available when the field does not raise a flag.<br/>Increase|decrease value to reflect the importance of this field in calculating the section and final score (Default value is 5).';

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $choices = $options['data']->getchoices();
        $section = isset( $options['section'] ) ? $options['section']->getId() : NULL;
        $form = isset( $options['form'] ) ? $options['form'] : NULL ;
        $archived = isset( $options['archived'] ) ? $options['archived'] : NULL ;
        $builder->add( 'id', 'hidden', array(
            'mapped'        => FALSE
        ));
        $builder->add( 'title', 'textarea', array(
            'attr'          => array(
                'placeholder'   => 'Title for this field',
                'class'         => '',
                'rows'          => 1,
            ),
            'required'      => TRUE,
        ));
        $builder->add( 'section', 'audit_section', array(
            'data'      => $section,
            'form'      => $form,
            'archived'  => $archived,
            'attr'          => array(
                'class'         => '',
            ),
        ));
        $builder->add( 'weight', 'integer', array(
            'attr'          => array(
                'title'     => self::TOOLTIPWEIGHT
            ),
        ));
        $builder->add( 'flag', 'checkbox', array(
            'label'         => 'Should this field raise a flag?',
            'required'      => FALSE,
            'attr'          => array(
                'class'         => 'cisco-audit-flag-ckbox',
                'title'         => self::TOOLTIPFLAG,
            ),
        ));
        $builder->add( 'description', 'textarea', array(
            'attr'          => array(
                'placeholder'   => 'Description for the field. This should be as clear as possible',
                'rows'          => 5,
            ),
        ));
        $builder->add( self::SCORE_YES, 'textarea', array(
            'mapped'        => FALSE,
            'required'      => FALSE,
            'data'          => isset( $choices[Score::YES] ) ? $choices[Score::YES] : '',
            'attr'          => array(
                'placeholder'   => 'Correct answer definition',
                'rows'          => 2,
            ),
        ));
        $builder->add( self::SCORE_NO, 'textarea', array(
            'mapped'        => FALSE,
            'required'      => FALSE,
            'data'          => isset( $choices[Score::NO] ) ? $choices[Score::NO] : '',
            'attr'          => array(
                'placeholder'   => 'Incorrect answer definition',
                'rows'          => 2,
            ),
        ));
        $builder->add( self::SCORE_ACCEPTABLE, 'textarea', array(
            'mapped'        => FALSE,
            'required'      => FALSE,
            'data'          => isset( $choices[Score::ACCEPTABLE] ) ? $choices[Score::ACCEPTABLE] : '',
            'attr'          => array(
                'placeholder'   => 'Partially correct answer definition',
                'title'         => self::TOOLTIPOPTIONAL,
                'rows'          => 2,
            ),
            'label'         => 'Acceptable',
        ));
        $builder->add( self::SCORE_NOT_APPLICABLE, 'textarea', array(
            'mapped'        => FALSE,
            'required'      => FALSE,
            'data'          => isset( $choices[Score::NOT_APPLICABLE] ) ? $choices[Score::NOT_APPLICABLE] : '',
            'attr'          => array(
                'placeholder'   => 'Answer not applicable',
                'title'         => self::TOOLTIPOPTIONAL,
                'rows'          => 2,
            ),
            'label'         => 'N/A',
        ));
    }

    public function getName()
    {
        return 'field';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class'    => 'CiscoSystems\AuditBundle\Entity\Field',
            'section'       => NULL,
            'form'          => NULL,
            'archived'      => NULL,
        ));
    }

    /**
     * Convenience method for setting a non-mapped field from the form data
     *
     * @param CiscoSystems\AuditBundle\Entity\Field $entity
     * @param array $values
     */
    static public function mapScores( Field $entity, $values )
    {
        $extraFields = array(
            Score::YES => self::SCORE_YES,
            Score::NO => self::SCORE_NO,
            Score::ACCEPTABLE => self::SCORE_ACCEPTABLE,
            Score::NOT_APPLICABLE => self::SCORE_NOT_APPLICABLE,
        );
        foreach ( $extraFields as $key => $extraField )
        {
            if ( isset( $values[ $extraField ] ) && $values[ $extraField ] )
            {
                $entity->addChoice( $key, $values[ $extraField ] );
            }
        }
    }
}
