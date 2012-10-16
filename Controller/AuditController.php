<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Form\Type\AuditScoreType;
use WG\AuditBundle\Entity\Audit;
use WG\AuditBundle\Entity\AuditScore;

class AuditController extends Controller
{
    /**
     * View created audits
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:Audit' );
        $auditlist = $repo->findAll();
        return $this->render( 'WGAuditBundle:Audit:index.html.twig', array(
            'audits' => $auditlist,
        ));
    }
    
    /**
     * Create new audit
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundException
     */
    public function addAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'id' ) );
        if ( null === $auditform )
        {
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        if ( null !== $scores = $request->get( 'score' ) )
        {
            $fieldRepo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
            $audit = new Audit();
            $audit->setAuditForm( $auditform );
            // depending on configuration, set a user ID:
            if ( $this->container->getParameter( 'wg.audit.user.class' ) )
            {
                $user = $this->container->get( 'security.context' )->getToken()->getUser();
                $prop = $this->container->getParameter( 'wg.audit.user.property' );
                if ( $user && $prop )
                {
                    $method = 'get' . ucfirst( $prop );
                    $audit->setAuditingUser( $user->$method() );
                }
            }
            // TODO: add input field to form in order to set a reference
            // $audit->setAuditReference( null );
            // ...
            //
            $em->persist( $audit );
            foreach ( $scores as $fieldId => $scoreData )
            {
                $field = $fieldRepo->find( $fieldId );
                $score = new AuditScore();
                $score->setAudit( $audit );
                $score->setField( $field );
                $score->setScore( $scoreData['value'] );
                $score->setComment( $scoreData['comment'] );
                $em->persist( $score );
            }
            // TODO: calculate result and display / send emails / whatever, depending on configuration
            $audit->setFailed( false ); // replace with result of calculation
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgaudits' ) );
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        return $this->render( 'WGAuditBundle:Audit:add.html.twig', array(
            'auditform' => $auditform,
            'scoreform' => $scoreform->createView(),
        ));
    }

    /**
     * view a single Audit
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundException
     */
    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:Audit' );
        $audit = $repo->find( $request->get( 'id' ) );
        if ( null === $audit )
        {
            throw $this->createNotFoundException( 'Audit not found' );
        }
        return $this->render( 'WGAuditBundle:Audit:view.html.twig', array(
            'audit' => $audit,
        ));
    }

}
