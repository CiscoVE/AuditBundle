<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\SectionField;
use CiscoSystems\AuditBundle\Form\Type\FieldType;
use CiscoSystems\AuditBundle\Entity\Score;
use CiscoSystems\AuditBundle\Form\Type\SectionType;

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
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $field = new Field();
        if ( $id = $request->get( 'field_id' ))
        {
            $edit = true;
            $field = $em->getRepository( 'CiscoSystemsAuditBundle:Field' )
                        ->find( $id );
            if( !$field )
            {
                throw $this->createNotFoundException( 'No field was found for field id #' . $id . '.' );
            }
        }
        $section = new Section();
        if( $request->get( 'section_id' ) )
        {
            $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                          ->find( $request->get( 'section_id' ));
            $field->setSection( $section );
        }
        $form = $this->createForm( new FieldType(), $field, array(
            'section' => $field->getSection(),
        ));
        if ( null !== $values = $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $flaggedField = $field->getFlag();
                $allowMultipleAnswer = $field->getSection()->getForm()->getAllowMultipleAnswer();
                $this->mapScores( $field, $values, $flaggedField, $allowMultipleAnswer );
                $em->persist( $field );
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audit_section_edit', array(
                    'section_id'    => $field->getSection()->getId(),
                )));
            }
        }
        /**
         * NO currently used: planned to load into a modal box
         */
//        if ( $request->isXmlHttpRequest())
//        {
//            return $this->render( 'CiscoSystemsAuditBundle:Field:_edit.html.twig', array(
//                'edit'  => $edit,
//                'field' => $field,
//                'form'  => $form->createView(),
//            ));
//        }
//        else
//        {
            return $this->render( 'CiscoSystemsAuditBundle:Field:edit.html.twig', array(
                'edit'      => $edit,
                'field'     => $field,
                'form'      => $form->createView(),
            ));
//        }
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
        if ( null !== $field = $repo->find( $request->get( 'field_id' ) ))
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
        if ( null !== $field = $repo->find( $request->get( 'field_id' ) ))
        {
            //
            $section = $field->getSection();
            $scores = $em->getRepository( 'CiscoSystemsAuditBundle:Score' )
                         ->findAll();
            if ( null !== $scores = $field->getScores()) $field->removeAllScores();
            if ( null !== $section = $field->getSection()) $section->removeField( $field );

            $field->setSection( null );
            $em->remove( $field );
            $em->flush();

            return $this->redirect( $this->generateUrl( 'audit_section_edit', array (
                'section_id'  => $section->getId(),
            )));
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
        $em = $this->getDoctrine()->getEntityManager();

        foreach( $scores[0] as $score )
        {
            $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
            $field = $repo->find( $score[0] );
            $value = Score::getWeightPercentageForScore( $score[1] );
            $weight = $field->getWeight();
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
        $this->get( 'ladybug' )->log( $field );
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

    public function disableAction( Request $request )
    {
        return $this->render( 'Action not implemented yet.' );
    }

    public function changeSectionAction( Request $request )
    {
        return $this->render( 'Action not implemented yet.' );
    }
}