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
                ) );
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
            $userClass = $this->container->getParameter( 'wg.audit.user.class' );
            if ( $userClass )
            {
                $prop = $this->container->getParameter( 'wg.audit.user.property' );
                $user = $this->container->get( 'security.context' )->getToken()->getUser();
                if ( $user instanceof $userClass && $prop )
                {
                    $method = 'get' . ucfirst( $prop );
                    if ( method_exists( $user, $method ) )
                    {
                        $audit->setAuditingUser( $user->$method() );
                    }
                }
            }
            // TODO: add input field to form in order to set a reference
            // $audit->setAuditReference( null );
            // ...
            //
            //$globalScore = 0;

            $em->persist( $audit );
            foreach ( $scores as $fieldId => $scoreData )
            {
                $field = $fieldRepo->find( $fieldId );
                $score = new AuditScore();
                $score->setAudit( $audit );
                $score->setField( $field );
                $score->setScore( $scoreData[ 'value' ] );
                $score->setComment( $scoreData[ 'comment' ] );
                $globalScore += $score->getWeightPercentage();
                $em->persist( $score );
            }
            $audit->setWeightPercentage( $globalScore );

            // TODO: calculate result and display / send emails / whatever, depending on configuration
            $audit->setFailed( false ); // replace with result of calculation
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgaudits' ) );
        }
        $scoreform = $this->createForm( new AuditScoreType() );
        return $this->render( 'WGAuditBundle:Audit:add.html.twig', array(
                    'auditform' => $auditform,
                    'scoreform' => $scoreform->createView(),
                ) );
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
        $audit = $auditrepo->find( $request->get( 'id' ) );
        if ( null === $audit )
        {
            throw $this->createNotFoundException( 'Audit not found' );
        }
        else
        {
            $scorerepo = $em->getRepository( 'WGAuditBundle:AuditScore' );
            $scores = $scorerepo->findBy( array( 'audit' => $audit ) );
            // audit weight percentage
            $audit->setWeightPercentage($this->getWeightPercentage($scores));
            $auditweight = 0;
            
            // section weight percentage + weight
            $sections = $audit->getAuditForm()->getSections();
            foreach($sections as $section)
            {
                $sectionWeight = 0;
                $sectionWeightPercentage = 0;
                $fields = $section->getFields();

                foreach ($fields as $field)
                {
                    $fieldScores = $scorerepo->findBy(array('field' => $field));
                    
                    $sectionWeight += $field->getWeight();
                    $sectionWeightPercentage += $this->getWeightPercentage( $fieldScores);      
//                    $section->addWeightPercentage($this->getWeightPercentage( $fieldScores));
//                    $section->addWeight($field->getWeight());
                }
                
                $section->setWeight($sectionWeight);
                $section->setWeightPercentage($sectionWeightPercentage);
                $auditweight += $section->getWeight();
            }
            
            $audit->setWeight($auditweight);
        }

        return $this->render( 'WGAuditBundle:Audit:view.html.twig', array(
            'audit' => $audit,
            'scores' => $scores,
        ));
    }
    
    public function getWeightPercentage( $scores )
    {
        $weightPercentage = 0;
        
        foreach ( $scores as $score )
        {
            $weightPercentage += $score->getWeightPercentage();
        }
        
        return $weightPercentage;
    }
}
