<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\FormSection;
use CiscoSystems\AuditBundle\Entity\SectionField;
use CiscoSystems\AuditBundle\Form\Type\SectionType;

class SectionController extends Controller
{
    public function indexAction()
    {
        $sections = $this->getDoctrine()
                         ->getEntityManager()
                         ->getRepository( 'CiscoSystemsAuditBundle:Section' )
                         ->findAll();

        return $this->render( 'CiscoSystemsAuditBundle:Section:index.html.twig', array(
            'sections' => $sections,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $section = new Section();
//        $relation = new FormSection();
        if ( '' !== $sid = $request->get( 'section_id' ) )
        {
            $edit = true;
            $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                          ->find( $sid );
            if( !$section )
            {
                throw $this->createNotFoundException( 'No field was found for id #' . $sid . '.' );
            }
        }
        $auditForm = null;
        if( $request->get( 'form_id' ) )
        {
            $auditForm = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
                            ->find( $request->get( 'form_id' ) );
//            $relation->setForm( $auditForm );
//            $relation->setSection( $section );
//            $auditForm->AddSectionRelation( $relation );
            $auditForm->addSection( $section );
        }
        $form = $this->createForm( new SectionType(), $section );
        if ( null !== $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
//                $em->persist( $relation );
                $em->persist( $auditForm );
                $em->persist( $section );
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audit_form_edit', array(
                    'form_id'  => $auditForm->getId(),
                )));
            }
        }

        $uFields = $em->getRepository( 'CiscoSystemsAuditBundle:Field' )
                      ->getArchivedFields();
//                      ->getDetachedFields();
//                      ->getUnAssignedFields( $section );

        /**
         * Performed for ajax request; Planned to be used with a modal box
        if ( $request->isXmlHttpRequest() )
        {
            return $this->render( 'CiscoSystemsAuditBundle:Section:_edit.html.twig', array(
                'edit'      => $edit,
                'section'   => $section,
                'ufields'   => $uFields,
                'form'      => $form->createView(),
            ));
        }
        else*/ return $this->render( 'CiscoSystemsAuditBundle:Section:edit.html.twig', array(
            'edit'          => $edit,
            'section'       => $section,
            'ufields'       => $uFields,
            'form'          => $form->createView(),
        ));
    }

    /**
     * Performed for ajax request
     * Planned to be used with a modal box
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     * @throws type
     */
    public function viewAction( Request $request )
    {
        $sectionRepo = $this->getDoctrine()
                            ->getEntityManager()
                            ->getRepository( 'CiscoSystemsAuditBundle:Section' );
        if ( null === $section = $sectionRepo->find( $request->get( 'section_id' ) ))
        {
            throw $this->createNotFoundException( 'Section does not exist' );
        }

        return $this->render( 'CiscoSystemsAuditBundle:Section:view.html.twig', array(
            'section' => $section,
        ));
    }

    /**
     * Delete Section and remove all reference from Form
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return twig template
     *
     * @throws type
     */
    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Section' );
        if ( null !== $section = $repo->find( $request->get( 'section_id' ) ))
        {
//            $formRepo = $em->getRepository( 'CiscoSystemsAuditBundle:Form' );
            $form = $section->getForm();
            $id = $form->getId();
            $section->setForm( null );
            $section->removeAllField();
            $em->remove( $section );
            $em->flush();

            return $this->redirect( $this->generateUrl( 'audit_form_edit', array(
                'form_id'   => $id,
            )));
        }
        throw $this->createNotFoundException( 'Section does not exist' );
    }

    /**
     * Remove field from section based on the section.id and field.id passed in
     * the url.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws type
     */
    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                      ->find( $request->get( 'section_id' ));
        if ( null !== $section )
        {
            $fieldRepo = $em->getRepository( 'CiscoSystemsAuditBundle:Field' );
            $field = $fieldRepo->find( $request->get( 'field_id' ));
            if ( null !== $field )
            {
                $section->removeField( $field );
                $em->persist( $section );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    $fields = $fieldRepo->findBy( array ( 'section' => null ));

                    return $this->render( 'CiscoSystemsAuditBundle:Field:_ulist.html.twig', array(
                        'ufields'   => $fields,
                        'section'   => $section,
                    ));
                }
                else
                {
                    return $this->redirect( $this->generateUrl( 'audit_section_edit', array (
                        'section_id' => $section->getId() )
                    ));
                }
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws type
     */
    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $section = $em->getRepository( 'CiscoSystemsAuditBundle:Section' )
                      ->find( $request->get( 'section_id' ));
        if ( null !== $section )
        {
            $field = $em->getRepository( 'CiscoSystemsAuditBundle:Field' )
                        ->find( $request->get( 'field_id' ));
            if ( null !== $field )
            {
                $section->addField( $field );
                $em->persist( $section );
                $em->flush();
                if ( $request->isXmlHttpRequest() )
                {
                    return $this->render( 'CiscoSystemsAuditBundle:Field:_load.html.twig', array(
                        'field'    => $field,
                        'section'  => $section,
                    ));
                }
                else
                {
                    return $this->redirect( $this->generateUrl( 'audit_section_edit', array (
                        'section_id' => $section->getId() )
                    ));
                }
            }
            throw $this->createNotFoundException( 'Field does not exist' );
        }
        throw $this->createNotFoundException( 'Section does not exist' );
    }
}
