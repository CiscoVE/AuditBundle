<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WG\AuditBundle\Entity\AuditFormField;
use WG\AuditBundle\Form\Type\AuditFormFieldType;

class AuditFormFieldController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $fields = $repo->findAll();
        return $this->render( 'WGAuditBundle:AuditFormField:index.html.twig', array(
            'fields' => $fields,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $field = new AuditFormField();
        if ( $request->get( 'id' ) )
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
                return $this->redirect( $this->generateUrl( 'wgauditformfields' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormField:edit.html.twig', array(
            'edit' => $edit,
            'form' => $form->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        if ( null === $field = $repo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->render( 'WGAuditBundle:AuditFormField:view.html.twig', array(
            'field' => $field,
        ));
    }

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        if ( null !== $field = $repo->find( $request->get( 'id' ) ))
        {
            $em->remove( $field );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgauditformfields' ));
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
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $field = $repo->find( $request->get( 'id' ));
        if ( null === $field )
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }

        $value = $request->get('scoreData');

        $sectionRepo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $section = $sectionRepo->find( $field->getSection()->getId());
        $ret = 100;
//        $section->addScore($field->getWeight(), $value);
//        $formRepo = $em->getRepositoty( 'WGAuditBundle:AuditForm' );
//        $auditForm = $formRepo->find( $section->getAuditform());

//        $ret = 'whatever';
        //
        // Do your calculation here
        //
        return new Response( $ret );
    }

}