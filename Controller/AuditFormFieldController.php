<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditFormField;
use WG\AuditBundle\Entity\AuditScore;
use WG\AuditBundle\Form\Type\AuditFormFieldType;

class AuditFormFieldController extends Controller
{

    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $fields = $repo->findAll();
        $field = new AuditFormField();
        return $this->render( 'WGAuditBundle:AuditFormField:index.html.twig', array(
                    'field' => $field,
                    'fields' => $fields,
                ) );
    }

    public function editAction( Request $request )
    {
        $request->getSession()->getFlashBag()->add( 'fieldId', $request->get( 'id' ) );
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );

        $fieldId = $request->get( 'id' );
        if ( null !== $fieldId )
        {
            $field = $repo->find( $fieldId );

            if ( !$field )
            {
                throw $this->createNotFoundException( 'Field does not exist' );
            }
        }
        else
        {
            $field = new AuditFormField();
        }

        $form = $this->createForm( new AuditFormFieldType(), $field );
        if ( null !== $values = $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $extraFields = array(
                    AuditScore::YES => 'answer_yes',
                    AuditScore::NO => 'answer_no',
                    AuditScore::ACCEPTABLE => 'answer_acceptable',
                    AuditScore::NOT_APPLICABLE => 'answer_not_applicable',
                );
                foreach ( $extraFields as $key => $extraField )
                {
                    if ( isset( $values[ $extraField ] ) && $values[ $extraField ] )
                    {
                        $field->addScore( $key, $values[ $extraField ] );
                    }
                }
                $em->persist( $field );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditformfields' ) );
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormField:edit.html.twig', array(
                    'form' => $form->createView(),
                ) );
    }

    public function viewAction( Request $request )
    {
        $request->getSession()->getFlashBag()->add( 'fieldId', $request->get( 'id' ) );
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $field = $repo->find( $request->get( 'id' ) );
        if ( null !== $field )
        {
            $form = $this->createForm( new AuditFormFieldType(), $field );
        }
        else
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->render( 'WGAuditBundle:AuditFormField:view.html.twig', array(
                    'field' => $field,
                ) );
    }

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $field = $repo->find( $request->get( 'id' ) );
        if ( null !== $field )
        {
            $em->remove( $field );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->redirect( $this->generateUrl( 'wgauditformfields' ) );
    }

}