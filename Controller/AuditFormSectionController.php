<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditFormSection;
use WG\AuditBundle\Form\Type\AuditFormSectionType;

class AuditFormSectionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $sections = $repo->findAll();
        $section = new AuditFormSection();
        return $this->render( 'WGAuditBundle:AuditFormSection:index.html.twig', array(
            'section' => $section,
            'sections' => $sections,
        ));
    }
    
    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $section = new AuditFormSection();        
        if ( $request->get( 'id' ) )
        {
            $edit = true;
            $section = $repo->find( $request->get( 'id' ));
        }
        $form = $this->createForm( new AuditFormSectionType(), $section);
        if ( null !== $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $section );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditformsections' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormSection:edit.html.twig', array(
            'edit' => $edit,
            'form' => $form->createView(),
        ));
    }
    
    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        if ( null === $section = $repo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->render( 'WGAuditBundle:AuditFormSection:view.html.twig', array(
            'section' => $section,
        ));        
    }
    
    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        if ( null !== $section = $repo->find( $request->get( 'id' ) ))
        {
            $em->remove( $section );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgauditformsections' ));
        }
        throw $this->createNotFoundException( 'Field does not exist' );        
    }
}
