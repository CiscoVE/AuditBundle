<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditSection;
use WG\AuditBundle\Form\Type\AuditSectionType;

class AuditFormSectionController extends Controller
{
    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
        $sections = $repo->findAll();
        $section = new AuditSection();
        $form = $this->createForm( new AuditSectionType(), $section );
        if ( null !== $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $section );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'auditsections' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormSection:index.html.twig', array(
            'sections' => $sections,
            'form' => $form->createView(),
        ));
    }
}
