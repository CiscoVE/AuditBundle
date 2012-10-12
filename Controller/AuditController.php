<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AuditController extends Controller
{

    // view list of Audit available

    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:Audit' );
        $auditlist = $repo->findAll();

        $newAudit = new AuditForm();
        $form = $this->createForm( new AuditFormType(), $newAudit );
        if ( null !== $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $newAudit );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditlist' ) );
            }
        }
        return $this->render( 'WGAuditBundle:Audit:index.html.twig', array(
                    'audits' => $auditlist,
                    'form' => $form->createView(),
                ) );
    }

    // view a single Audit
    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:Audit' );

        $anAudit = $repo->find( $request->get( 'id' ) );

        if ( null !== $anAudit )
        {
            $this->createNotFoundException( 'Audit not found' );
        }
        return $this->render( 'WGAuditBundle:Audit:view.html.twig', array(
                    'audit' => $anAudit,
                    'form' => $form->createView(),
                ) );
    }

}