<?php
/**
 * User: Dred
 * Date: 16.12.13
 * Time: 14:36
 */

namespace Domain\CoreBundle\Command\Task;

use Domain\CoreBundle\Command\Abstracts\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppointmentReminderCommand
 */
class AppointmentAutoReminderCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:reminder:auto')->setDescription('Send reminder to experts and candidates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $appointmentRepository = $container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CoreBundle:Appointment');

        $appointmentManager = $container->get('domain.appointment.manager');
        $appointmentManager->unsetActor();

        $params = $this->getContainer()->getParameter('appointment_manager');

        if (empty($params['auto_notifications']['enable'])) {
            $output->writeln('<comment>Auto-notification is disabled.</comment>');
            return;
        }

        $interval = !empty($params['auto_notifications']['interval']) ?
            intval($params['auto_notifications']['interval']) : 0;

        //retrieve appointments for candidate
        $appointments = $appointmentRepository->getNotAutoNotificated($interval);

        $return = count($appointments).' - auto reminders.';

        foreach ($appointments as $appointment) {
            $appointmentManager
                ->setAppointment($appointment)
                ->autoRemind()
            ;
        }

        $output->writeln('<info>'.$return.'</info>');
    }
}
 