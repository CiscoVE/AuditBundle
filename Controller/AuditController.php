<?php

namespace CiscoSystems\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CiscoSystems\AuditBundle\Form\Type\AuditType;
use CiscoSystems\AuditBundle\Form\Type\ScoreType;
use CiscoSystems\AuditBundle\Entity\Audit;
use CiscoSystems\AuditBundle\Entity\Form;
use CiscoSystems\AuditBundle\Entity\Section;
use CiscoSystems\AuditBundle\Entity\Field;
use CiscoSystems\AuditBundle\Entity\Score;

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
        $repo = $em->getRepository( 'CiscoSystemsAuditBundle:Form' );
        $auditform = $repo->find( $request->get( 'form' ) );
        if ( null === $auditform )
        {
            // Throw error 404 if audit form data not found
            throw $this->createNotFoundException( 'Audit form not found' );
        }
        // Create new audit instance
        $audit = new Audit();
        $audit->setForm( $auditform );
                echo "<div>foo</div"; die();
        // Create form object for audit
        $form = $this->createForm( $this->container->get( 'cisco.formtype.audit' ), $audit );
        if ( 'POST' == $request->getMethod() )
        {
                echo "<div>foo</div"; die();
            // bind request for form object
            $form->bind( $request );
            $scores = $request->get( 'score' );
            if ( null !== $scores && $form->isValid() )
            {
                $this->setUser( $audit );
                $this->setScores( $em, $audit, $scores );
                $audit->setMark( $scoreService->getResultForAudit( $audit ));
                $em->persist( $audit );
                $em->flush();

                return $this->redirect( $this->generateUrl( 'audits' ) );
            }
        }
        $scoreform = $this->createForm( new ScoreType() );

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
                $audit->setAuditor( $user );
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
    protected function setScores( $entityMgr, Audit $audit, $scores )
    {
        $fieldRepo = $entityMgr->getRepository( 'CiscoSystemsAuditBundle:Field' );
        foreach ( $scores as $fieldId => $scoreData )
        {
            $field = $fieldRepo->find( $fieldId );
            $score = new Score();
            $score->setField( $field );
            $score->setMark( $scoreData[ 'value' ] );
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

        if ( null !== $audit )
        {
            foreach( $audit->getForm()->getSections( TRUE ) as $section )
            {
                $scoreService->setFlagForSection( $audit, $section );
            }
            if ( null !== $audit->getForm() )
            {
                return $this->render( 'CiscoSystemsAuditBundle:Audit:view.html.twig', array(
                    'audit'     => $audit,
                ));
            }
            else
                return $this->redirect( $this->generateUrl( 'audits' ) );
        }
        else
            throw $this->createNotFoundException( 'Audit not found' );
    }

    public function exportAction( Request $request )
    {
        $caseId = $request->get( 'id' );

        $em = $this->getDoctrine()->getManager();
        $audit = $em->getRepository( 'CiscoSystemsAuditBundle:Audit' )
                    ->findOneBy( array( 'id' => $caseId ));
        $scores = $em->getRepository( 'CiscoSystemsAuditBundle:Score' )
                     ->findBy( array( 'audit' => $audit ) );

        $scoringService = $this->get( 'cisco.worker.audit_score' );

        // ask the service for a Excel5
        $excelService = $this->get( 'xls.service_xls5' );

        // create the object see http://phpexcel.codeplex.com documentation
        $properties = $excelService->excelObj->getProperties();
        $properties->setCreator( 'Cisco Systems' );
        $properties->setLastModifiedBy( 'Cisco Systems' );
        $properties->setTitle( 'Audit Bundle Export File' );
        $properties->setSubject( 'Audit Bundle Export File' );
        $properties->setDescription( 'Audit Review spreadsheet automatically exported.' );
        $properties->setKeywords( 'audit review spreadsheet' );
        $properties->setCategory( 'audit Review Export' );

        $rowCounter = 2;
        $seCounter = 1;
        $fiCounter = 1;
        $excelService->excelObj->setActiveSheetIndex(0);
        $sheet = $excelService->excelObj->getActiveSheet();

        /**
         * Header
         */
        $sheet->setCellValue( 'B2', 'Case #' . $audit->getReference() );
        $sheet->setCellValue( 'C2', 'Engineer ' . $audit->getAuditor() );
        $sheet->setCellValue( 'D2', 'Score' );
        $sheet->setCellValue( 'B3', 'S.No.' );
        $sheet->setCellValue( 'E3', 'Weight' );
        $sheet->setCellValue( 'F3', 'Reviewer\'s Comment' );
        $rowCounter++;

        $form = $em->getRepository( 'CiscoSystemsAuditBundle:Form' )
                   ->getState( $audit );

        /**
         * content for the audit
         */
        foreach( $form->getSections() as $section )
        {
            $sheet->setCellValue( 'C' . $rowCounter, 'Section ' . $seCounter . ': ' . $section->getTitle());
            $rowCounter++;

            foreach( $section->getFields() as $field )
            {
                $sheet->setCellValue( 'B' . $rowCounter, $fiCounter );
                $sheet->setCellValue( 'C' . $rowCounter, $field->getTitle() );
                $value = null;

                foreach( $scores as $score )
                {
                    if( $score->getField() === $field )
                    {
                        $sheet->setCellValue( 'D' . $rowCounter, $score->getMark());
                        $sheet->setCellValue( 'F' . $rowCounter, $score->getComment());

                        $value = ( $field->getFlag() && $score->getMark()  === 'N' ) ?
                                 $audit->getForm()->getFlagLabel() :
                                 $field->getWeight() ;
                    }
                }

                $sheet->setCellValue( 'E' . $rowCounter, $value );
                $fiCounter++; $rowCounter++;
            }

            $sheet->setCellValue( 'D' . $rowCounter, $scoringService->getResultForSection( $audit, $section ));
            $sheet->setCellValue( 'E' . $rowCounter, $scoringService->getWeightForSection( $section ));

            $seCounter++; $rowCounter++;
        }

        $sheet->setCellValue( 'C' . $rowCounter, 'Final Score on the Case' );

        $result = ( $audit->getFlag() ) ?
                  $audit->getForm()->getFlagLabel() :
                  $scoringService->getResultForAudit( $audit ) ;

        $sheet->setCellValue( 'D' . $rowCounter, $result );
        $sheet->setCellValue( 'E' . $rowCounter, $scoringService->getWeightForAudit( $audit ));

        $sheet->setTitle( 'Case #' . $caseId );
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->excelObj->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        $response->headers->set( 'Content-Type', 'text/vnd.ms-excel; charset=utf-8' );
        $filename = 'case-review-' . $audit->getId() .'.xls' ;

        $response->headers->set( 'Content-Disposition', 'attachment;filename=' . $filename );

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set( 'Pragma', 'public' );
        $response->headers->set( 'Cache-Control', 'maxage=1' );

        return $response;
    }
}