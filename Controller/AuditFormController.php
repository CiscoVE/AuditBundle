<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditForm;
use WG\AuditBundle\Form\Type\AuditFormType;

class AuditFormController extends Controller
{
    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
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
                return $this->redirect( $this->generateUrl( 'wgauditforms' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditForm:index.html.twig', array(
            'forms' => $formlist,
            'form' => $form->createView(),
        ));
    }

}

