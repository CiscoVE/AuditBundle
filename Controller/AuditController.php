<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CiscoSystems\AuditBundle\Form\Type\AuditType;
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
        $auditform = $repo->find( $request->get( 'id' ) );
        $audit->setAuditForm( $auditform );

        $form = $this->createForm( $this->container->get( 'cisco.formtype.audit' ), $audit );

        if ( null === $auditform )
        {
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        if ( null !== $scores = $request->get( 'score' ) /* && $form->isValid() */ )
        {
            $this->setUser( $audit );
            $this->setAuditScores( $em, $audit, $scores );
            $audit->setTotalScore( $audit->getTotalResult() );
            $em->persist( $audit );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'cisco_audits' ) );
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        $routes = $this->get( 'router' )->getRouteCollection();
        return $this->render( 'CiscoSystemsAuditBundle:Audit:add.html.twig', array(
            'audit'                      => $audit,
//            'form' => $form->createView(),
            'scoreform'                  => $scoreform->createView(),
            'routePatternCalculateScore' => $routes->get( 'cisco_auditformfield_calculate_score' )->getPattern(),
        ));
    }

    /**
     * Set the user from the context
     * 
     * @param type $audit
     */
    private function setUser( $audit )
    {
        $token = $this->container->get( 'security.context' )->getToken();
        if ( $token )
        {
            $user = $token->getUser();
            if ( $user )
            {
                $audit->setAuditingUser( $user );
            }
        }
    }

    /**
     * find all scores and persist them against the relevant fields
     * 
     * @param type $entityMgr
     * @param type $audit
     * @param type $scores
     */
    private function setAuditScores( $entityMgr, $audit, $scores )
    {
        $fieldRepo = $entityMgr->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        foreach ( $scores as $fieldId => $scoreData )
        {
            $field = $fieldRepo->find( $fieldId );
            $score = new AuditScore();
//            $score->setAudit( $audit );
            $score->setField( $field );
            $score->setScore( $scoreData[ 'value' ] );
            $score->setComment( $scoreData[ 'comment' ] );
            $audit->addScore( $score );
            $entityMgr->persist( $score );
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
        $audit = $auditrepo->find( $request->get( 'id' ) );

        if ( null !== $audit )
        {
            if ( null !== $audit->getAuditForm() )
            {
                $scorerepo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditScore' );
                $scores = $scorerepo->findBy( array( 'audit' => $audit ) );
                return $this->render( 'CiscoSystemsAuditBundle:Audit:view.html.twig', array(
                    'audit'  => $audit,
                    'scores' => $scores,
                ));
            }
            else return $this->redirect( $this->generateUrl( 'cisco_audits' ) );
        }
        else throw $this->createNotFoundException( 'Audit not found' );
    }
}