<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditFormSection;
use WG\AuditBundle\Form\Type\AuditFormSectionType;

class AuditFormSectionController extends Controller
{
    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $sections = $repo->findAll();
        $section = new AuditFormSection();
        $form = $this->createForm( new AuditFormSectionType(), $section );
        if ( null !== $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $section );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'auditformsections' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormSection:index.html.twig', array(
            'sections' => $sections,
            'form' => $form->createView(),
        ));
    }
}
