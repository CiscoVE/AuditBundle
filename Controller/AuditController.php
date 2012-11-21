<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Form\Type\AuditScoreType;
use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\AuditScore;

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
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Audit' );
        $audits = $repo->findAll();
        return $this->render( 'CiscoSystemsAuditBundle:Audit:index.html.twig', array(
            'audits' => $audits,
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
        $audit = new Audit();

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'id' ));
        $audit->setAuditForm( $auditform );

        if ( null === $auditform )
        {
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        if ( null !== $scores = $request->get( 'score' ))
        {
            // depending on configuration, set a user ID:
            $this->setUserID( $audit );
            // TODO: add input field to form in order to set a reference
            // $audit->setAuditReference( null );
            $this->setAuditScores( $em, $audit, $scores );
            $em->persist( $audit );
            // TODO: calculate result and display / send emails / whatever, depending on configuration
            // replace with result of calculation
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_audits' ));
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        $routes = $this->get( 'router' )->getRouteCollection();
        return $this->render( 'CiscoSystemsAuditBundle:Audit:add.html.twig', array(
            'audit' => $audit,
            'scoreform' => $scoreform->createView(),
            'routePatternCalculateScore' => $routes->get( 'cisco_auditformfield_calculate_score' )->getPattern(),
        ));
    }

    private function setUserID( $audit )
    {
        $userClass = $this->container->getParameter( 'wg.audit.user.class' );
        if ( $userClass )
        {
            $prop = $this->container->getParameter( 'wg.audit.user.property' );
            $user = $this->container->get( 'security.context' )->getToken()->getUser();
            if ( $user instanceof $userClass && $prop )
            {
                $method = 'get' . ucfirst( $prop );
                if ( method_exists( $user, $method ))
                {
                    $audit->setAuditingUser( $user->$method() );
                }
            }
        }
    }

    private function setAuditScores( $entityMrg, $audit, $scores )
    {
        $fieldRepo = $entityMrg->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        foreach ( $scores as $fieldId => $scoreData )
        {
            $field = $fieldRepo->find( $fieldId );
            $score = new AuditScore();
            $score->setAudit( $audit );
            $score->setField( $field );
            $score->setScore( $scoreData[ 'value' ] );
            $score->setComment( $scoreData[ 'comment' ] );
            $entityMrg->persist( $score );
        }
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
        $auditrepo = $em->getRepository( 'CiscoSystemsAuditBundle:Audit' );
        $audit = $auditrepo->find( $request->get( 'id' ));
        if ( null === $audit )
        {
            throw $this->createNotFoundException( 'Audit not found' );
        }
        else
        {
            $scorerepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditScore' );
            $scores = $scorerepo->findBy( array( 'audit' => $audit ));
        }
        return $this->render( 'CiscoSystemsAuditBundle:Audit:view.html.twig', array(
            'audit' => $audit,
            'scores' => $scores,
        ));
    }

    public function showIconSetAction()
    {
        return $this->render( 'CiscoSystemsAuditBundle:Audit:iconset.html.twig' );
    }
}