<?php

namespace CiscoSystems\AuditBundle\Worker;

use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Score;

class Scoring
{
    /**
     * Get the weight percentage for a specific score
     *
     * @param \CiscoSystems\AuditBundle\Entity\Score $score
     *
     * @return integer
     */
    public function getWeightPercentageForScore( Score $score )
    {
        switch( $score->getMark() )
        {
            case Score::YES:
            case Score::NOT_APPLICABLE: return 100;
            case Score::ACCEPTABLE: return 50;
            case Score::NO: break;
        }
        return 0;
    }


    /**
     * Get Score for Field
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\Field $field
     *
     * @return \CiscoSystems\AuditBundle\Entity\Score
     */
    public function getScoreForField( Audit $audit, Field $field )
    {
        foreach ( $audit->getScores() as $score )
        {
            if ( null !== $score->getField() && $field === $score->getField() )
            {
                return $score;
            }
        }

        return FALSE;
    }

    /**
     * Get the total weight for the section. returns 1 IF the weight has been set to 0.
     *
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return integer
     */
    public function getWeightForSection( Audit $audit, Section $section )
    {
        $index = $audit->getFormIndexes();
        $weight = 0;
        foreach( $section->getFields() as $field )
        {
            if( FALSE === in_array( $field->getId(), $index['fields']) ) continue;
            $weight += $field->getWeight() ;
        }

        return $weight;
    }

    /**
     * Get Score for Section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return integer
     */
    public function getResultForSection( Audit $audit, Section $section )
    {
        $index = $audit->getFormIndexes();
        $fields = $section->getFields();
        $fieldCount = count( $fields );
        if ( 0 == $fieldCount ) return 100;
        $achievedPercentages = 0;

        foreach ( $fields as $field )
        {
            if( FALSE === in_array( $field->getId(), $index['fields']) ) continue;
            $score = $this->getScoreForField( $audit, $field );

            if ( !$score )
            {
                $score = new Score();
                $score->setMark( Score::YES );
            }

            $achievedPercentages += $this->getWeightPercentageForScore( $score );
        }
        return number_format( $achievedPercentages / $fieldCount, 2, '.', '' );
    }

    /**
     * Find the trigger value for the section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     */
    public function setFlagForSection( Audit $audit, Section $section )
    {
        $index = $audit->getFormIndexes();
        foreach ( $section->getFields() as $field )
        {
//            echo($field->getTitle()); die;
//            echo($audit->getId()); die;
            if( FALSE === in_array( $field->getId(), $index['fields']) ) continue;
            $mark = $this->getScoreForField( $audit, $field )->getMark();
            if ( $field->getFlag() === TRUE && $mark === Score::NO )
            {
                $section->setFlag( TRUE );
            }
        }
    }

    /**
     * Get the flag value for the section
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param \CiscoSystems\AuditBundle\Entity\Section $section
     *
     * @return boolean
     */
    public function getFlagForSection( Audit $audit, Section $section )
    {
        $index = $audit->getFormIndexes();
        foreach( $section->getFields() as $field )
        {
            if( FALSE === in_array( $field->getId(), $index['fields']) ) continue;
            if( $field->getFlag() === TRUE && $this->getScoreForField( $audit, $field )->getMark() === Score::NO )
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Get final score for the audit
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return integer
     */
    public function getResultForAudit( Audit $audit )
    {
        if ( null !== $auditform = $audit->getForm() )
        {
            $sections = $auditform->getSections();
            $count = count( $sections );
            if ( 0 == $count ) return 100;
            $totalPercent = 0;
            $divisor = 0;
            $audit->setFlag( FALSE );
            $index = $audit->getFormIndexes();
            foreach ( $sections as $section )
            {
                if( FALSE === in_array( $section->getId(), $index['sections']) ) continue;
                $percent = $this->getResultForSection( $audit, $section );
                $weight = $this->getWeightForSection( $audit, $section );
                $sectionFlag = $this->getFlagForSection( $audit, $section );

                if ( $sectionFlag ) $audit->setFlag( TRUE );
                $divisor += $weight;
                if( $divisor > 0 )
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
     * Get total weight for the audit
     *
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     *
     * @return integer
     */
    public function getWeightForAudit( Audit $audit )
    {
        $index = $audit->getFormIndexes();

        echo '<div>'; print_r($index); echo '</div>';
        echo '<div>'; print_r($audit->getFormState()); echo '</div>';

        $weight = 0;
        foreach ( $audit->getForm()->getSections() as $section )
        {
            if( FALSE === in_array( $section->getId(), $index['sections']) ) continue;
            $weight += $this->getWeightForSection( $audit, $section );
        }

        return $weight;
    }
}