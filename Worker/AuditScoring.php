<?php

namespace CiscoSystems\AuditBundle\Worker;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditScore;

class AuditScoring
{
    /**
     * Get the weight percentage for a specific score
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditScore $score
     *
     * @return integer
     */
    public function getWeightPercentageForScore( AuditScore $score )
    {
        switch( $score->getScore() )
        {
            case AuditScore::YES:
            case AuditScore::NOT_APPLICABLE: return 100;
            case AuditScore::ACCEPTABLE: return 50;
            case AuditScore::NO: break;
        }
        return 0;
    }


    /**
     * Get Score for Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormField $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditScore
     */
    public function getScoreForField( Audit $audit, AuditFormField $field )
    {
        foreach ( $audit->getScores() as $score )
        {
            if ( null !== $score->getField() && $field === $score->getField() )
            {
                return $score;
            }
        }

        return false;
    }

    /**
     * Get the total weight for the section
     *
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     *
     * @return integer
     */
    public function getWeightForSection( AuditFormSection $section )
    {
        $weight = 0;
        foreach( $section->getFields() as $field )
        {
            $weight +=  ( $field->getFlag() === TRUE && $field->getWeight() < 1 ) ?
                        1 :
                        $field->getWeight() ;
        }

        return $weight;
    }

    /**
     * Get Score for Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     *
     * @return integer
     */
    public function getResultForSection( Audit $audit, AuditFormSection $section )
    {
        $fields = $section->getFields();
        $fieldCount = count( $fields );
        if ( 0 == $fieldCount ) return 100;
        $achievedPercentages = 0;

        foreach ( $fields as $field )
        {
            $score = $this->getScoreForField( $audit, $field );

            if ( !$score )
            {
                $score = new AuditScore();
                $score->setScore( AuditScore::YES );
            }

            $achievedPercentages += $this->getWeightPercentageForScore( $score );
        }
        return number_format( $achievedPercentages / $fieldCount, 2, '.', '' );
    }

    /**
     * Find the trigger value for the section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     */
    public function setFlagForSection( Audit $audit, AuditFormSection $section )
    {
        foreach ( $section->getFields() as $field )
        {
            if ( $field->getFlag() == true &&  $this->getScoreForField( $audit, $field )->getScore() == AuditScore::NO )
            {
                $section->setFlag( true );
            }
        }
    }

    /**
     * Get global score
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return integer
     */
    public function getResultForForm( Audit $audit )
    {
        if ( null !== $auditform = $audit->getAuditForm() )
        {
            $sections = $auditform->getSections();
            $count = count( $sections );
            if ( 0 == $count ) return 100;
            $totalPercent = 0;
            $divisor = 0;
            $audit->setFlag( false );

            foreach ( $sections as $section )
            {
                $percent = $this->getResultForSection( $audit, $section );
                $weight = $this->getWeightForSection( $section );
                $this->setFlagForSection( $audit, $section );
                if ( $section->getFlag() ) $audit->setFlag( true );
                $divisor += $weight;

                // check the section for flag not set and section's weight > 0
                if( $section->getFlag() === false && $divisor > 0 )
                {
                    $totalPercent = $totalPercent * ( $divisor - $weight ) / $divisor + $percent * $weight / $divisor;
                }
            }

            return number_format( $totalPercent, 2, '.', '' );
        }
        else
            return 0;
    }

    /**
     * Get global weight
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return integer
     */
    public function getWeightForForm( Audit $audit )
    {
        $weight = 0;
        foreach ( $audit->getAuditForm()->getSections() as $section )
        {
            $weight += $this->getWeightForSection( $section );
        }

        return $weight;
    }
}