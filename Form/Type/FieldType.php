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
    const TOOLTIPFLAG = '<i class="icon-exclamation-sign icon-white"/> If this is enable, some of the fields below will not be editable.';
    const TOOLTIPOPTIONAL = '<i class="icon-exclamation-sign icon-white"/> Only available when the form is allowing for multiple answers and the field is not set to raise a flag.';
    const TOOLTIPWEIGHT = '<i class="icon-exclamation-sign icon-white"/> Only available when the field does not raise a flag.<br/>Increase|decrease value to reflect the importance of this field in calculating the section and final score (Default value is 5).';


    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $scores = $options['data']->getScores();
        $builder->add( 'id', 'hidden', array(
            'mapped'        => false
        ));
        $builder->add( 'title', 'textarea', array(
            'attr'          => array(
                'placeholder'   => 'Title for this field',
                'class'         => 'input-xxlarge',
                'rows'          => 1,
            ),
            'required'      => true,
        ));
        $builder->add( 'section', 'entity', array(
            'empty_data'    => '---',
            'required'      => false,
            'class'         => 'CiscoSystemsAuditBundle:Section',
            'property'      => 'title',
            'empty_value'   => '(Choose a Section)',
            'attr'          => array(
                'class'         => 'input-xlarge',
            ),
        ));
        $builder->add( 'weight', 'integer', array(
            'attr'          => array(
                'title'                 => self::TOOLTIPWEIGHT,
//                'data-original-title'   => self::TOOLTIPWEIGHT,
                'data-toggle'   => 'tooltip',
                'class'         => 'input-mini',
            ),
        ));
        $builder->add( 'flag', 'checkbox', array(
            'label'         => 'Should this field raise a flag?',
            'required'      => false,
            'attr'          => array(
                'class'         => 'cisco-audit-flag-ckbox',
                'title'         => self::TOOLTIPFLAG,
//                'data-original-title'   => self::TOOLTIPFLAG,
                'data-toggle'   => 'tooltip',
            ),
        ));
        $builder->add( 'description', 'textarea', array(
            'attr'          => array(
                'placeholder'   => 'Description for the field. This should be as clear as possible',
                'class'         => 'input-xxlarge',
                'rows'          => 5,
            ),
        ));
        $builder->add( self::SCORE_YES, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[Score::YES] ) ? $scores[Score::YES] : '',
            'attr'          => array(
                'placeholder'   => 'Correct answer definition',
                'class'         => 'input-xxlarge',
                'rows'          => 2,
            ),
        ));
        $builder->add( self::SCORE_NO, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[Score::NO] ) ? $scores[Score::NO] : '',
            'attr'          => array(
                'placeholder'   => 'Incorrect answer definition',
                'class'         => 'input-xxlarge',
                'rows'          => 2,
            ),
        ));
        $builder->add( self::SCORE_ACCEPTABLE, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[Score::ACCEPTABLE] ) ? $scores[Score::ACCEPTABLE] : '',
            'attr'          => array(
                'placeholder'           => 'Partially correct answer definition',
                'title'                 => self::TOOLTIPOPTIONAL,
//                'data-original-title'   => self::TOOLTIPOPTIONAL,
                'data-toggle'   => 'tooltip',
                'class'         => 'input-xxlarge',
                'rows'          => 2,
            ),
            'label'         => 'Acceptable',
        ));
        $builder->add( self::SCORE_NOT_APPLICABLE, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[Score::NOT_APPLICABLE] ) ? $scores[Score::NOT_APPLICABLE] : '',
            'attr'          => array(
                'placeholder'     => 'Answer not applicable',
                'title'           => self::TOOLTIPOPTIONAL,
//                'data-original-title'   => self::TOOLTIPOPTIONAL,
                'data-toggle'   => 'tooltip',
                'class'         => 'input-xxlarge',
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
            'data_class' => 'CiscoSystems\AuditBundle\Entity\Field',
            'section'    => null,
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
