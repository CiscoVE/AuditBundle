<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\AuditForm;
use CiscoSystems\AuditBundle\Form\Type\AuditFormType;

class AuditFormController extends Controller
{

    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $formlist = $repo->findAll();
        $newform = new AuditForm();
        $form = $this->createForm( new AuditFormType(), $newform );
        if ( null !== $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $newform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_auditforms' ) );
            }
        }
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:index.html.twig', array(
            'forms' => $formlist,
            'form'  => $form->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        if ( null === $form = $repo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Form does not exist' );
        }
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:view.html.twig', array(
            'form' => $form,
        ));
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $formlist = $repo->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:list.html.twig', array(
            'forms' => $formlist,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = new AuditForm();
        if ( $request->get( 'id' ) )
        {
            $edit = true;
            $auditform = $repo->find( $request->get( 'id' ));
        }
        $form = $this->createForm( new AuditFormType(), $auditform);
        if ( null !== $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $auditform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_auditforms' ));
            }
        }
        $routes = $this->get( 'router' )->getRouteCollection();
        $uSectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $uSections = $uSectionRepo->findBy( array ( 'auditForm' => null ));
        $uFieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $uFields = $uFieldRepo->findBy( array ( 'section' => null ));
        
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:edit.html.twig', array(
            'edit'                  => $edit,
            'auditform'             => $auditform,
            'usections'             => $uSections,
            'ufields'               => $uFields,
            'form'                  => $form->createView(),
            'routePatternRemove'    => $routes->get( 'cisco_auditform_remove' )->getPattern(),
            'routePatternLoad'      => $routes->get( 'cisco_auditformsection_load' )->getPattern(),
            'routePatternLoadField' => $routes->get( 'cisco_auditformfield_load' )->getPattern(),
        ));
    }

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        if ( null !== $form = $repo->find( $request->get( 'id' ) ))
        {
            $form->removeAllAudit();
            $em->remove( $form );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_auditforms' ));
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    // TODO: change this to have an ajax request
    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'id' ));
        if ( null !== $auditform )
        {
            $sectionRep = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
            $section = $sectionRep->find( $request->get( 'section_id' ));
            if ( null !== $section )
            {
                $auditform->removeSection( $section );
                $em->persist( $auditform );
                $em->flush();
                return new Response();
                //return $this->redirect( $this->generateUrl( 'cisco_auditforms' ));
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'id' ));
        if ( null != $auditform )
        {
            $sectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
            $section = $sectionRepo->find( $request->get( 'section_id' ));
            if ( null != $section )
            {
                $auditform->addSection( $section );
                $em->persist( $auditform );
                $em->flush();
                if ( $request->isXmlHttpRequest() ) return new Response();
                else return $this->redirect( $this->generateUrl( 'cisco_auditform_edit', array (
                    'id' => $auditform->getId() )
                ));
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'AuditForm does not exist' );
    }
}
