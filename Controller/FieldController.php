<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Form\Type\FieldType;
use CiscoSystems\AuditBundle\Entity\Score;
use CiscoSystems\AuditBundle\Form\Type\SectionType;
use Symfony\Component\VarDumper\VarDumper;

class FieldController extends Controller
{
    /**
     * List all fields
     *
     * @return twig template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
        $fields = $repo->findAll();

        return $this->render( 'CiscoSystemsAuditBundle:Field:index.html.twig', array(
            'fields' => $fields,
        ));
    }

    /**
     * Edit existing field or create new field
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return twig template
     */
    public function editAction( Request $request )
    {
        $edit = FALSE;
        $em = $this->getDoctrine()->getEntityManager();
        $field = new Field();
        $fid = $request->get( 'field_id' );
        if ( '' !== $fid && NULL !== $fid )
        {
            $edit = TRUE;
            $field = $em->getRepository( 'CiscoSystemsAuditBundle:Field' )
                        ->find( $fid );
            if( !$field )
            {
                throw $this->createNotFoundException( 'No field was found for id #' . $fid . '.' );
            }
        }
        $section = new Section();
        if( $request->get( 'section_id' ) )
        {
            $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                          ->find( $request->get( 'section_id' ));
            if( NULL === $field->getId() ) $field->addSection( $section );
        }
        $auditform = FALSE === $section->getForm() ? NULL : $section->getForm();
        $form = $this->createForm( new FieldType(), $field, array(
            'section'   => $field->getSection(),
            'form'      => $auditform,
            'archived'  => FALSE,
        ));
        if ( NULL !== $values = $request->get( $form->getName() ))
        {
            $clone = clone $field;
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $flaggedField = $field->getFlag();
                $allowMultipleAnswer = $field->getSection()->getForm()->getAllowMultipleAnswer();
                if( $edit && NULL !== $field->compare( $clone ) )
                {
                    $section->removeField( $field );

                    $newField = new Field();
                    $newField->setTitle( $field->getTitle() )
                             ->setDescription( $field->getDescription() )
                             ->setWeight( $field->getWeight() )
                             ->setChoices( $field->getChoices() )
                             ->setFlag( $field->getFlag() )
                             ->setCritical( $field->getCritical() )
                             ->setNumericalScore( $field->getNumericalScore() )
                             ->setIsRemoveFromCalculations( $field->getIsRemoveFromCalculations() )
                             ->addSections( $field->getSections() );
                    $em->persist( $newField );
                }
                else
                {
                    $this->mapScores( $field, $values, $flaggedField, $allowMultipleAnswer );
                    $em->persist( $field->getSection() );
                    $em->persist( $field );
                }
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audit_section_edit', array(
                    'section_id'    => $field->getSection()->getId(),
                )));
            }
        }
        /**
         * NO currently used: planned to load into a modal box
         */
        if ( $request->isXmlHttpRequest())
        {
            return $this->render( 'CiscoSystemsAuditBundle:Field:_edit.html.twig', array(
                'edit'  => $edit,
                'field' => $field,
                'form'  => $form->createView(),
            ));
        }
        else
        {
            return $this->render( 'CiscoSystemsAuditBundle:Field:edit.html.twig', array(
                'edit'      => $edit,
                'field'     => $field,
                'form'      => $form->createView(),
            ));
        }
    }

    /**
     * Returns an array of choices from the given Form
     *
     * @param \Symfony\Component\Form\Form $form
     *
     * @return array
     */
    private function getChoices( Form $form )
    {
        return array(
            'Y' => $form[FieldType::SCORE_YES]->getData(),
            'N' => $form[FieldType::SCORE_NO]->getData(),
            'A' => $form[FieldType::SCORE_ACCEPTABLE]->getData(),
            'NA' => $form[FieldType::SCORE_NOT_APPLICABLE]->getData()
        );
    }

    /**
     * Convenience method for setting a non-mapped field from the form data
     *
     * @param CiscoSystems\AuditBundle\Entity\Field $entity
     * @param array $values
     */
    private function mapScores( Field $entity, $values, $triggerFlag, $multipleAnswer )
    {
        $extraFields = array(
            Score::YES => FieldType::SCORE_YES,
            Score::NO => FieldType::SCORE_NO,
        );

        $flaggedFields = array();
        if( !( $triggerFlag === TRUE && $multipleAnswer === FALSE ))
        {
            $flaggedFields = array(
                Score::ACCEPTABLE => FieldType::SCORE_ACCEPTABLE,
                Score::NOT_APPLICABLE => FieldType::SCORE_NOT_APPLICABLE,
            );
        }
        $entity->setChoices( NULL );
        foreach ( array_merge( $extraFields, $flaggedFields ) as $key => $field )
        {
            if ( isset( $values[ $field ] ) && $values[ $field ] )
            {
                $entity->addChoice( $key, $values[ $field ] );
            }
        }
    }

    /**
     * View single field
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return twig template
     *
     * @throws type
     */
    public function viewAction( Request $request )
    {
        $repo = $this->getDoctrine()
                     ->getEntityManager()
                     ->getRepository( 'CiscoSystemsAuditBundle:Field' );
        if ( NULL !== $field = $repo->find( $request->get( 'field_id' ) ))
        {
            if ( $request->isXmlHttpRequest())
            {
                return $this->render( 'CiscoSystemsAuditBundle:Field:_view.html.twig', array(
                    'field' => $field,
                ));
            }
            else
            {
                return $this->render( 'CiscoSystemsAuditBundle:Field:view.html.twig', array(
                    'field' => $field,
                ));
            }
        }

        throw $this->createNotFoundException( 'Field does not exist' );
    }

    /**
     * Delete field and remove all relation to score and section
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return twig template
     *
     * @throws type
     */
    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
        $relationRepo = $em->getRepository(  'CiscoSystemsAuditBundle:SectionField' );
        if ( NULL !== $field = $repo->find( $request->get( 'field_id' ) ))
        {
            $relations = $relationRepo->getRelationPerField( $field );
            foreach( $relations as $relation )
            {
                $relation->setArchived( TRUE );
                $em->persist( $relation );
            }
            $em->flush();

            return $this->redirect( $this->generateUrl( 'audit_forms' ));
        }
        throw $this->createNotFoundException( 'Field does not exist' );
    }

    /**
     * Get weight percentage from $request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws type
     */
    public function calculateScoreAction( Request $request )
    {
        $scores[] = $request->request->get( 'scores' );
        $sectionWeight = 0;
        $tempScore = 0;
        $nonCompliant = false;
        $em = $this->getDoctrine()->getEntityManager();

        foreach( $scores[0] as $score )
        {
            $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
            $field = $repo->find( $score[0] );
            
            if ($field->getIsRemoveFromCalculations())
                continue;
            $value = Score::getWeightPercentageForScore( $score[1] );
            $weight = $field->getWeight();
            if ($field->getCritical() && $value === 0)
                return new Response(json_encode($value));
            $tempScore += $value * $weight;
            $sectionWeight += $weight;
        }

        $sectionScore = $tempScore / $sectionWeight;

        return new Response( json_encode( $sectionScore ));
    }

    /**
     * Load field
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return twig template
     */
    public function loadAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $field = $em->getRepository( 'CiscoSystemsAuditBundle:Field' )
                    ->find( $request->get( 'field_id' ));
        $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                      ->find( $field-getAudit()->getId() );

        return $this->render( 'CiscoSystemsAuditBundle:Field:_load.html.twig', array(
            'field' => $field,
            'section' => $section,
        ));
    }

    public function listAction( Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
        $fieldId = $request->get( 'fieldId' );
        $field = $repo->findBy( array( 'id' => $fieldId ));
//        $this->get( 'ladybug' )->log( $field );
//        $section = $field->getSection();
//        $this->get( 'ladybug' )->log( $field->getSection() );
        $sectionform = $this->createFormBuilder()->add( 'audit_section', new SectionType( $em ), array(
//            'section' => $field->getSection()->getId(),
        ))->getForm();

        return $this->render( 'CiscoSystemsAuditBundle:Section:_select.html.twig', array(
            'fields' => $repo->findAll(),
            'form'   => $sectionform->createView(),
        ));
    }
}