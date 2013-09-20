<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Form\Type\FormType;

class FormController extends Controller
{

    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $formlist = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
//                       ->getArchived( FALSE );
                       ->findAll();
        $newform = new Form();
        $auditform = $this->createForm( new FormType(), $newform );
        if ( null !== $request->get( $auditform->getName() ) )
        {
            $auditform->bind( $request );
            if ( $auditform->isValid() )
            {
                $em->persist( $newform );
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audit_forms' ) );
            }
        }

        return $this->render( 'CiscoSystemsAuditBundle:Form:index.html.twig', array(
            'forms' => $formlist,
            'form'  => $auditform->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Form' );
        if ( null === $auditform = $repo->find( $request->get( 'form_id' ) ))
        {
            throw $this->createNotFoundException( 'Form does not exist' );
        }

        return $this->render( 'CiscoSystemsAuditBundle:Form:view.html.twig', array(
            'form' => $auditform,
        ));
    }

    public function listAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $formlist = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
                       ->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:Form:list.html.twig', array(
            'forms' => $formlist,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Form' );
        $auditform = new Form();
        $uSections = array();
        $sectionRepo = $em->getRepository( 'CiscoSystemsAuditBundle:Section' );
        $fid = $request->get( 'form_id' );
        if ( '' !== $fid && NULL !== $fid )
        {
            $edit = true;
            $auditform = $repo->find( $request->get( 'form_id' ));
            $uSections = $sectionRepo->getUnAssignedPerForm( $auditform );
        }
        else
        {
            $uSections = $sectionRepo->getUnAssignedPerForm();
        }
        $form = $this->createForm( new AuditFormType(), $auditform);
        if ( null !== $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $auditform );
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audit_forms' ));
            }
        }

        if ( $request->isXmlHttpRequest())
        {
            return $this->render( 'CiscoSystemsAuditBundle:Field:_edit.html.twig', array(
                'edit'          => $edit,
                'auditform'     => $auditform,
                'form'          => $form->createView(),
            ));
        }
        else
        {
            return $this->render( 'CiscoSystemsAuditBundle:Form:edit.html.twig', array(
                'edit'                  => $edit,
                'auditform'             => $auditform,
                'usections'             => $uSections,
                'form'                  => $form->createView(),
            ));
        }
    }

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Form' );
        $relationRepo = $em->getRepository( 'CiscoSystemsAuditBundle:FormSection' );
        if ( null !== $form = $repo->find( $request->get( 'form_id' ) ))
        {
            $relations = $relationRepo->getRelationPerForm( $form );
            foreach( $relations as $relation )
            {
                $relation->setArchived( TRUE );
                $em->persist( $relation );
            }
            $em->flush();

            return $this->redirect( $this->generateUrl( 'audit_forms' ));
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $auditform = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
                        ->find( $request->get( 'form_id' ));
        if ( null !== $auditform )
        {
            $sectionRep = $em->getRepository( 'CiscoSystemsAuditBundle:Section' );
            $section = $sectionRep->find( $request->get( 'section_id' ));
            if ( null !== $section )
            {
                $auditform->removeSection( $section );
                $em->persist( $auditform );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    $sections = $sectionRep->getUnAssignedPerForm( $auditform );

                    return $this->render( 'CiscoSystemsAuditBundle:Section:_ulist.html.twig', array(
                        'auditform'  => $auditform,
                        'usections'  => $sections,
                    ));
                }
                else
                {
                    return $this->redirect( $this->generateUrl( 'audit_form_edit', array (
                            'form_id' => $auditform->getId() )
                    ));
                }
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    /**
     * Add section to the form and either redirect to the edit form template or
     * send the section _load template
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return template|render
     *
     * @throws NotFoundException
     */
    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $auditform = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
                        ->find( $request->get( 'form_id' ));
        if ( null !== $auditform )
        {
            $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                          ->find( $request->get( 'section_id' ));
            if ( null !== $section )
            {
                $auditform->addSection( $section );
                $em->persist( $section );
                $em->persist( $auditform );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    return $this->render( 'CiscoSystemsAuditBundle:Section:_load.html.twig', array (
                        'auditform' => $auditform,
                        'section'   => $section,
                    ));
                }
                else
                {
                    return $this->redirect( $this->generateUrl( 'audit_form_edit', array (
                        'form_id' => $auditform->getId() )
                    ));
                }
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }
}
