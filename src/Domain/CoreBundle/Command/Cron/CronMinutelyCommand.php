<?php
/**
 * User: Dred
 * Date: 18.12.13
 * Time: 14:56
 */

namespace Domain\CoreBundle\Command\Cron;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class CronMinutelyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cron:minutely')
            ->setDescription('Run every minute.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobManager = $this->getContainer()->get('domain.core.job.manager');

        $jobManager->regularAppointmentReminder();

        $jobManager->regularAppointmentComplete();

        $jobManager->regularSentEmails();

        $jobManager->regularCloseOverdueAppointments();

        $params = $this->getContainer()->getParameter('appointment_manager');
        if (!empty($params['auto_notifications']['enable'])) {
            $jobManager->regularAppointmentAutoReminder();
        }

        $jobManager->updateVideoStatuses();

        $output->writeln('<info>Completed.</info>');
    }
}
 