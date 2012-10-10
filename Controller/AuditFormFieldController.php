<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditFormField;
use WG\AuditBundle\Entity\AuditScore;
use WG\AuditBundle\Form\Type\AuditFormFieldType;

class AuditFormFieldController extends Controller
{
    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditFormField' );
        $fields = $repo->findAll();
        $field = new AuditFormField();
        $form = $this->createForm( new AuditFormFieldType(), $field );
        if ( null !== $values = $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $extraFields = array(
                    AuditFieldScore::YES => 'answer_yes',
                    AuditFieldScore::NO => 'answer_no',
                    AuditFieldScore::ACCEPTABLE => 'answer_acceptable',
                    AuditFieldScore::NOT_APPLICABLE => 'answer_not_applicable',
                );
                foreach ( $extraFields as $key => $extraField )
                {
                    if ( isset( $values[$extraField] ) && $values[$extraField] )
                    {
                        $field->addScore( $key, $values[$extraField] );
                    }
                }
                $em->persist( $field );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'auditfields' ) );
            }
        }
        return $this->render( 'WGAuditBundle:AuditFormField:index.html.twig', array(
            'fields' => $fields,
            'form' => $form->createView(),
        ));
    }
}