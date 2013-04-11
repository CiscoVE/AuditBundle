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
        $auditform = $this->createForm( new AuditFormType(), $newform );
        if ( null !== $request->get( $auditform->getName() ) )
        {
            $auditform->bind( $request );
            if ( $auditform->isValid() )
            {
                $em->persist( $newform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_auditforms' ) );
            }
        }
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:index.html.twig', array(
            'forms' => $formlist,
            'form'  => $auditform->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        if ( null === $auditform = $repo->find( $request->get( 'form_id' ) ))
        {
            throw $this->createNotFoundException( 'Form does not exist' );
        }
        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:view.html.twig', array(
            'form' => $auditform,
        ));
    }

    public function listAction( Request $request )
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
        if ( $request->get( 'form_id' ) )
        {
            $edit = true;
            $auditform = $repo->find( $request->get( 'form_id' ));
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
        $uSectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $uSections = $uSectionRepo->findBy( array ( 'auditForm' => null ));

        return $this->render( 'CiscoSystemsAuditBundle:AuditForm:edit.html.twig', array(
            'edit'                  => $edit,
            'auditform'             => $auditform,
            'usections'             => $uSections,
            'form'                  => $form->createView(),
        ));
    }

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        if ( null !== $auditform = $repo->find( $request->get( 'form_id' ) ))
        {
            $em->remove( $auditform );
            $auditform->removeAllSection();
            $auditform->removeAllAudit();
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_auditforms' ));
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'form_id' ));
        if ( null !== $auditform )
        {
            $sectionRep = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
            $section = $sectionRep->find( $request->get( 'section_id' ));
            if ( null !== $section )
            {
                $auditform->removeSection( $section );
                $em->persist( $auditform );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    $sections = $sectionRep->findBy( array ( 'auditForm' => null ));
                    return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:_ulist.html.twig', array(
                        'auditform'  => $auditform,
                        'usections'  => $sections,
                    ));
                }
                else return $this->redirect( $this->generateUrl( 'cisco_auditform_edit', array (
                    'id' => $auditform->getId() )
                ));
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'form_id' ));
        if ( null != $auditform )
        {
            $sectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
            $section = $sectionRepo->find( $request->get( 'section_id' ));
            if ( null != $section )
            {
                $auditform->addSection( $section );
                $em->persist( $auditform );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:_load.html.twig', array (
                        'auditform' => $auditform,
                        'section'   => $section,
                    ));
                }
                else return $this->redirect( $this->generateUrl( 'cisco_auditform_edit', array (
                    'id' => $auditform->getId() )
                ));
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'AuditForm does not exist' );
    }
}
