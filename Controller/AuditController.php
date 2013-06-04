<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     *
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws NotFoundException
     */
    public function addAction( Request $request )
    {
        $scoreService = $this->get( 'cisco.worker.audit_score' );
        // Grab the entity manager from the container
        $em = $this->getDoctrine()->getEntityManager();
        // Check for audit form data to be used
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'form' ) );
        if ( null === $auditform )
        {
            // Throw error 404 if audit form data not found
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        // Create new audit instance
        $audit = new Audit();
        $audit->setAuditForm( $auditform );
        // Create form object for audit
        $form = $this->createForm( $this->container->get( 'cisco.formtype.audit' ), $audit );
        if ( 'POST' == $request->getMethod() )
        {
            // bind request for form object
            $form->bind( $request );
            $scores = $request->get( 'score' );
            if ( null !== $scores && $form->isValid() )
            {
                $this->setUser( $audit );
                $this->setAuditScores( $em, $audit, $scores );
                $audit->setTotalScore( $scoreService->getResultForAudit( $audit ));
                $em->persist( $audit );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'cisco_audits' ) );
            }
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        return $this->render( 'CiscoSystemsAuditBundle:Audit:add.html.twig', array(
            'audit'                      => $audit,
            'form'                       => $form->createView(),
            'scoreform'                  => $scoreform->createView(),
        ));
    }

    /**
     * Set the user from the context
     *
     * @param type $audit
     */
    protected function setUser( $audit )
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
     * @param \Doctrine\ORM\EntityManager $entityMgr
     * @param \CiscoSystems\AuditBundle\Entity\Audit $audit
     * @param array $scores
     */
    protected function setAuditScores( $entityMgr, Audit $audit, $scores )
    {
        $fieldRepo = $entityMgr->getRepository( 'CiscoSystemsAuditBundle:AuditFormField' );
        foreach ( $scores as $fieldId => $scoreData )
        {
            $field = $fieldRepo->find( $fieldId );
            $score = new AuditScore();
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws NotFoundException
     */
    public function viewAction( Request $request )
    {
        $scoreService = $this->get( 'cisco.worker.audit_score' );
        $em = $this->getDoctrine()->getEntityManager();
        $auditrepo = $em->getRepository( 'CiscoSystemsAuditBundle:Audit' );
        $audit = $auditrepo->find( $request->get( 'id' ) );

        foreach( $audit->getAuditForm()->getSections() as $section )
        {
            $scoreService->setFlagForSection( $audit, $section );
        }

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