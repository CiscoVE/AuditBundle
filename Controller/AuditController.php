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
        
        foreach ($auditlist as $audit)
        {
            $scorerepo = $em->getRepository( 'WGAuditBundle:AuditScore' );
            $this->populateAuditScore( $scorerepo, $audit);
        }
        
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
        $auditform = $repo->find( $request->get( 'id' ));
        if ( null === $auditform )
        {
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        if ( null !== $scores = $request->get( 'score' ))
        {
            $fieldRepo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
            $audit = new Audit();
            $audit->setAuditForm( $auditform );
            // depending on configuration, set a user ID:
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
            // TODO: add input field to form in order to set a reference
            // $audit->setAuditReference( null );
            $em->persist( $audit );
            foreach ( $scores as $fieldId => $scoreData )
            {
                $field = $fieldRepo->find( $fieldId );
                $score = new AuditScore();
                $score->setAudit( $audit );
                $score->setField( $field );
                $score->setScore( $scoreData[ 'value' ] );
                $score->setComment( $scoreData[ 'comment' ] );
                $em->persist( $score );
            }
            // $this->PopulateAuditScore($scorerepo, $audit);
            // TODO: calculate result and display / send emails / whatever, depending on configuration
            // replace with result of calculation
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgaudits' ));
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        $routes = $this->get( 'router' )->getRouteCollection();
        return $this->render( 'WGAuditBundle:Audit:add.html.twig', array(
            'auditform' => $auditform,
            'scoreform' => $scoreform->createView(),
            'routePatternCalculateScore' => $routes->get( 'wgauditformfield_calculate_score' )->getPattern(),
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
        $auditrepo = $em->getRepository( 'WGAuditBundle:Audit' );
        $audit = $auditrepo->find( $request->get( 'id' ));
        if ( null === $audit )
        {
            throw $this->createNotFoundException( 'Audit not found' );
        }
        else
        {
            $scorerepo = $em->getRepository( 'WGAuditBundle:AuditScore' );
            $scores = $scorerepo->findBy( array( 'audit' => $audit ));

            $this->PopulateAuditScore( $scorerepo, $audit);
        }
        return $this->render( 'WGAuditBundle:Audit:view.html.twig', array(
            'audit' => $audit,
            'scores' => $scores,
        ));
    }
    
    /**
     * Populate scores for specified Audit, Score Repository
     * 
     * @param repository $scorerepo
     * @param repository $audit
     */
    public function populateAuditScore( $scorerepo, $audit )
    {
        foreach ( $audit->getAuditForm()->getSections() as $section )
        {
            foreach ( $section->getFields() as $field )
            {
                $singleScore = $scorerepo->findOneBy( array( 'field' => $field, 'audit' => $audit ));
                
                if($field->getFatal() == true)
                {
                    if($singleScore->getScore() == "N")
                    {
                        $audit->setFailed(true);
                    }
                }
                else
                    $section->addScore( $field->getWeight(), $singleScore->getWeightPercentage() );
            }

            if($audit->getFailed() == true)
            {
                $audit->setWeight(0);
                $audit->setWeightPercentage(0);
            }
            else
                $audit->addScore( $section->getWeight(), $section->getWeightPercentage() );
        }
    }
}
