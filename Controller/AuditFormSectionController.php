<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WG\AuditBundle\Entity\AuditFormSection;
use WG\AuditBundle\Form\Type\AuditFormSectionType;

class AuditFormSectionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $sections = $repo->findAll();
        return $this->render( 'WGAuditBundle:AuditFormSection:index.html.twig', array(
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
        $routes = $this->get( 'router' )->getRouteCollection();
        return $this->render( 'WGAuditBundle:AuditFormSection:edit.html.twig', array(
            'edit' => $edit,
            'section' => $section,
            'form' => $form->createView(),
            'routePatternView' => $routes->get( 'wgauditformfield_view' )->getPattern(),
            'routePatternRemove' => $routes->get( 'wgauditformsection_remove' )->getPattern(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sectionRepo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        if ( null === $section = $sectionRepo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->render( 'WGAuditBundle:AuditFormSection:view.html.twig', array(
            'section' => $section,
        ));
    }

    public function deleteAction( Request $request )
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

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $section = $repo->find( $request->get( 'id' ));
        if ( null !== $section )
        {
            $fieldRep = $em->getRepository( 'WGAuditBundle:AuditFormField' );
            $field = $fieldRep->find( $request->get( 'field_id' ));
            if ( null !== $field )
            {
                $section->removeField( $field );
                $em->persist( $section );
                $em->flush();
                return new Response();
                // $this->redirect( $this->generateUrl( 'wgauditforms' ));
            }
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        throw $this->createNotFoundException( 'Section   does not exist' );
    }

    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $section = $repo->find( $request->get( 'id' ));

        $fieldRepo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $fields = $fieldRepo->findBy( array ( 'section' => null ));
    }
}
