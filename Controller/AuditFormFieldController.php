<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\AuditFormField;
use CiscoSystems\AuditBundle\Form\Type\AuditFormFieldType;
use CiscoSystems\AuditBundle\Entity\AuditScore;


class AuditFormFieldController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $fields = $repo->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:index.html.twig', array(
            'fields' => $fields,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $field = new AuditFormField();
        if ( $request->get( 'id' ))
        {
            $edit = true;
            $field = $repo->find( $request->get( 'id' ));
        }
        $form = $this->createForm( new AuditFormFieldType(), $field);
        if ( null !== $values = $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                AuditFormFieldType::mapScores( $field, $values );
                $em->persist( $field );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_auditformfields' ));
            }
        }
        
        if ( $request->isXmlHttpRequest()) return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:_edit.html.twig', array(
            'edit'  => $edit,
            'field' => $field,
            'form'  => $form->createView(),
        ));
        else return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:edit.html.twig', array(
            'edit'  => $edit,
            'field' => $field,
            'form'  => $form->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        if ( null !== $field = $repo->find( $request->get( 'id' ) ))
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

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        if ( null !== $field = $repo->find( $request->get( 'id' ) ))
        {
            $scoreRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditScore' );
            $scores = $scoreRepo->findAll();
            if ( null != $scores = $field->getAuditScores()) $field->removeAllAuditScore();
            if ( null !== $section = $field->getSection()) $section->removeField( $field );
            
            $field->setSection( null );
            $em->remove( $field );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_auditformfields' ));
        }
        throw $this->createNotFoundException( 'Field does not exist' );
    }

    /**
     * Get weight percentage from $request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     */
    public function calculateScoreAction( Request $request )
    {
//        echo "<pre>";
//        print_r( $request->get( 'scores' ) );
//        echo "</pre>";
//        die(); exit;
        
        $scores[] = $request->request->get( 'scores' );
        
//        $sectionScore = 0;
        $sectionWeight = 0;
        
        $tempScore = 0;
        
        foreach( $scores as $score )
        {
            $value = AuditScore::getWeightPercentageForScore( $score[0] );
            $weight = $score[1];
            
            $tempScore = $value * $weight;
            
            $sectionWeight += $weight;
        }
        
        $sectionScore = $tempScore / $sectionWeight;
        
        return new Response( json_encode($sectionScore));
    }

    public function loadAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $fieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $field = $fieldRepo->find( $request->get( 'id' ));
        $section = $repo->find( $field-getAudit()->getId() );

        return $this->render( 'CiscoSystemsAuditBundle:AuditFormField:_load.html.twig', array(
            'field' => $field,
            'section' => $section,
        ));
    }


    // TODO:
    // add new field (create new field)
}