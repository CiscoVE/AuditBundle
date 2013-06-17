<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use CiscoSystems\AuditBundle\Form\Type\AuditFormFieldType;
use CiscoSystems\AuditBundle\Entity\AuditScore;
use CiscoSystems\AuditBundle\Form\Type\SectionType;

class AuditFormFieldController extends Controller
{
    /**
     * List all fields
     *
     * @return twig template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $fields = $repo->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:index.html.twig', array(
            'fields' => $fields,
        ));
    }

    /**
     * Edit existing field or create new field
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return twig template
     */
    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $field = new AuditFormField();
        if ( $request->get( 'field_id' ))
        {
            $edit = true;
            $field = $repo->find( $request->get( 'field_id' ));
        }
        $section = new AuditFormSection();
        if( $request->get( 'section_id' ) )
        {
            $sectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
            $section = $sectionRepo->find( $request->get( 'section_id' ));
            $field->setSection( $section );
        }
        $form = $this->createForm( new AuditFormFieldType(), $field, array(
            'section' => $field->getSection(),
        ));
        if ( null !== $values = $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                AuditFormFieldType::mapScores( $field, $values );
                $em->persist( $field );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_auditsection_edit', array(
                    'section_id'  => $field->getSection()->getId(),
                )));
            }
        }
        /**
         * NO currently used: planned to load into a modal box
         */
//        if ( $request->isXmlHttpRequest())
//        {
//            return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:_edit.html.twig', array(
//                'edit'  => $edit,
//                'field' => $field,
//                'form'  => $form->createView(),
//            ));
//        }
//        else
//        {
            return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:edit.html.twig', array(
                'edit'      => $edit,
                'field'     => $field,
                'form'      => $form->createView(),
            ));
//        }
    }

    /**
     * View single field
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return twig template
     * @throws type
     */
    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        if ( null !== $field = $repo->find( $request->get( 'field_id' ) ))
        {
            if ( $request->isXmlHttpRequest()) return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:_view.html.twig', array(
                'field' => $field,
            ));
            else return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:view.html.twig', array(
                'field' => $field,
            ));
        }
        throw $this->createNotFoundException( 'Field does not exist' );
    }

    /**
     * Delete field and remove all relation to score and section
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return twig template
     * @throws type
     */
    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        if ( null !== $field = $repo->find( $request->get( 'field_id' ) ))
        {
            //
            $section = $field->getSection();
            $scoreRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditScore' );
            $scores = $scoreRepo->findAll();
            if ( null != $scores = $field->getAuditScores()) $field->removeAllAuditScore();
            if ( null !== $section = $field->getSection()) $section->removeField( $field );

            $field->setSection( null );
            $em->remove( $field );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_auditsection_edit', array (
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
            $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
            $field = $repo->find( $score[0] );
            $value = AuditScore::getWeightPercentageForScore( $score[1] );
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
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $fieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $field = $fieldRepo->find( $request->get( 'field_id' ));
        $section = $repo->find( $field-getAudit()->getId() );

        return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:_load.html.twig', array(
            'field' => $field,
            'section' => $section,
        ));
    }

    public function listAction( Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $fieldId = $request->get( 'fieldId' );
        $field = $repo->findBy( array( 'id' => $fieldId ));
        $this->get( 'ladybug' )->log( $field );
//        $section = $field->getSection();
//        $this->get( 'ladybug' )->log( $field->getSection() );
        $sectionform = $this->createFormBuilder()->add( 'audit_section', new SectionType( $em ), array(
//            'section' => $field->getSection()->getId(),
        ))->getForm();

        return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:_select.html.twig', array(
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