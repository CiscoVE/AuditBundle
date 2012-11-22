<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\AuditFormSection;
use CiscoSystems\AuditBundle\Form\Type\AuditFormSectionType;

class AuditFormSectionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $sections = $repo->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:index.html.twig', array(
            'sections' => $sections,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
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
                return $this->redirect( $this->generateUrl( 'cisco_auditformsections' ));
            }
        }
        $routes = $this->get( 'router' )->getRouteCollection();
        $uFieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        $uFields = $uFieldRepo->findBy( array ( 'section' => null ));

        if ( $request->isXmlHttpRequest() ) 
        {
            return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:_edit.html.twig', array(
                'edit' => $edit,
                'section' => $section,
                'ufields' => $uFields,
                'form' => $form->createView(),
            ));
        }
        else return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:edit.html.twig', array(
            'edit' => $edit,
            'section' => $section,
            'ufields' => $uFields,
            'form' => $form->createView(),
            'routePatternLoad' => $routes->get( 'cisco_auditformfield_load' )->getPattern(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        if ( null === $section = $sectionRepo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        return $this->render( 'CiscoSystemsAuditBundle:AuditFormSection:view.html.twig', array(
            'section' => $section,
        ));
    }

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        if ( null !== $section = $repo->find( $request->get( 'id' ) ))
        {
            $section->setAuditForm( null );
            $section->removeAllField();
            $em->remove( $section );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_auditformsections' ));
        }
        throw $this->createNotFoundException( 'Section does not exist' );
    }

    /**
     * Remove field from section based on the section.id and field.id passed in
     * the url.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     */
    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $section = $repo->find( $request->get( 'id' ));
        if ( null !== $section )
        {
            $fieldRep = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
            $field = $fieldRep->find( $request->get( 'field_id' ));
            if ( null !== $field )
            {
                $section->removeField( $field );
                $em->persist( $section );
                $em->flush();
                if ( $request->isXmlHttpRequest() ) return new Response();
                else return $this->redirect( $this->generateUrl( 'cisco_auditformsection_edit', array (
                    'id' => $section->getId() )
                ));
            }
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        throw $this->createNotFoundException( 'Section does not exist' );
    }

    /**
     * Add field to section based on the section.id and the field.id passed in
     * the url.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     */
    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormSection' );
        $section = $repo->find( $request->get( 'id' ));
        if ( null !== $section )
        {
            $fieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
            $field = $fieldRepo->find( $request->get( 'field_id' ));
            if ( null !== $field )
                {
                    $section->addField( $field );
                    $em->persist( $section );
                    $em->flush();
                    if ( $request->isXmlHttpRequest() ) return new Response();
                    else return $this->redirect( $this->generateUrl( 'cisco_auditformsection_edit', array (
                        'id' => $section->getId() )
                    ));
            }
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        throw $this->createNotFoundException( 'Section does not exist' );
    }
}
