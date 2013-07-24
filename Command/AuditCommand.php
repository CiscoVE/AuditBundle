<?php

namespace CiscoSystems\AuditBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AuditCommand extends ContainerAwareCommand
{
    protected $scoreService;

    /**
     * Initialize whatever variables you may need to store beforehand, also load
     * Doctrine from the Container
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function initialize( InputInterface $input, OutputInterface $output )
    {
        parent::initialize( $input, $output );
        $this->em = $this->getContainer()->get( 'doctrine' )->getEntityManager();
        $this->scoreService = $this->getContainer()->get( 'cisco.worker.audit_score' );
    }

   /**
    * Configure the task with options and arguments
    */
    protected function configure()
    {
        parent::configure();
        $this->setName( 'audit:score:regenerate' );
        $this->setDescription( 'regenerate all the audit\'s total score for which the current value is 0.' );
        $this->setDescription( 'you can pass an optional argument: \'id\' as a audit id' );
        $this->addArgument( 'id', InputArgument::OPTIONAL, 'single audit, for which the score needs to be regenerated.' );
        $this->addOption( 'override', null, InputOption::VALUE_NONE, 'If set, all audit will be processed.');
    }

    /**
     * Process the command to regenerate all audit total score
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return type
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $dialog = $this->getHelperSet()->get( 'dialog' );
        if( !$dialog->askConfirmation( $output, '<question>Do you want to regenerate the audit\'s total score?</question>', false ))
        {
            return;
        }
        $output->writeln( '<comment>Starting score regeneration process</comment>' );

        $id = $input->getArgument( 'id' );
        $override = $input->getOption( 'override' );
        $auditRepo = $this->em->getRepository( 'CiscoSystemsAuditBundle:Audit' );

        if( $id )
        {
            $audit = $auditRepo->findOneBy( array( 'id' => $id ));
        }
        else
        {
            $audits = $auditRepo->findAll();

            foreach( $audits as $audit )
            {
                if( $override )
                {
                    $audit->setMark( $this->scoreService->getResultForAudit( $audit ));
                    $output->writeln( '<comment>Audit #' . $audit->getId() . ' processed, with final score: ' . $audit->getMark() . '%</comment>' );
                }
                else
                {
                    if( $audit->getMark() === 0.00 )
                    {
                        $audit->setMark( $this->scoreService->getResultForAudit( $audit ));
                        $output->writeln( '<comment>[Processed]Audit #' . $audit->getId() . ' processed, with final score: ' . $audit->getMark() . '%</comment>' );
                    }
                    else
                    {
                        $output->writeln( '<comment>[Skipped]Audit #' . $audit->getId() . ' allready had a score set ( ' . $audit->getMark() . '% )</comment>' );
                    }
                }
            }
        }
//        $dialog = $this->getHelperSet()->get( 'dialog' );
        if( !$dialog->askConfirmation( $output, '<question>Do you want to flush those score to the database?</question>', false ))
        {
            return;
        }
        $output->writeln( '<comment>Starting flushing process</comment>' );
        foreach( $audits as $audit )
        {
            $this->em->persist( $audit );
        }
        $this->em->flush();
        $output->writeln( '<info>Audit\'s total score regenerated succesfully for ' . count( $audits ) . ' audit(s).</info>' );
    }
}