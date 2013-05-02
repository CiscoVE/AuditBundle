<?php

namespace CiscoSystems\AuditBundle\Worker;

class Score
{
    // calculate score based on the parameters passed to this class
    // this is to take the business logic out of the Audit Entity

    /**
     * Get Score for Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormField $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\AuditScore
     */
    public function getScoreForField( \CiscoSystems\AuditBundle\Entity\Audit $audit, \CiscoSystems\AuditBundle\Entity\AuditFormField $field )
    {
        $scores = $audit->getScores();

        foreach ( $scores as $score )
        {
            if ( null !== $score->getField() && $field === $score->getField() )
            {
                return $score;
            }
        }
        return false;
    }

    /**
     * Get Score for Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     *
     * @return integer
     */
    public function getResultForSection( \CiscoSystems\AuditBundle\Entity\Audit $audit, \CiscoSystems\AuditBundle\Entity\AuditFormSection $section )
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
            $achievedPercentages += $score->getWeightPercentage();
        }
        return number_format( $achievedPercentages / $fieldCount, 2, '.', '' );
    }

    /**
     * Find the trigger value for the section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\AuditFormSection $section
     */
    public function findFlagForSection( \CiscoSystems\AuditBundle\Entity\Audit $audit, \CiscoSystems\AuditBundle\Entity\AuditFormSection $section )
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
    public function getTotalResult( \CiscoSystems\AuditBundle\Entity\Audit $audit )
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
                $weight = $section->getWeight();
                $this->findFlagForSection( $audit, $section );

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
        else return 0;
    }

    /**
     * Get global weight
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return integer
     */
    public function getTotalWeight( \CiscoSystems\AuditBundle\Entity\Audit $audit )
    {
        $weight = 0;
        $sections = $audit->getAuditForm()->getSections();

        foreach ( $sections as $section )
        {
            $weight += $section->getWeight();
        }
        return $weight;
    }
}