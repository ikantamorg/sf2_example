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
class AppointmentReminderCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:reminder')->setDescription('Send reminder to experts and candidates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $appointmentRepository = $container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CoreBundle:Appointment');

        $appointmentManager = $container->get('domain.appointment.manager');
        $return = [];
        //retrieve appointments for candidate
        $appointments = $appointmentRepository->getNotNotificatedForCandidate();

        $return[] = count($appointments).' - reminders for candidates.';

        foreach ($appointments as $appointment) {
            $appointmentManager
                ->setCandidate($appointment->getCandidate())
                ->setAppointment($appointment)
                ->remind()
            ;
        }


        //
        $appointments = $appointmentRepository->getNotNotificatedForExpert();

        $return[] = count($appointments).' - reminders for Experts.';

        foreach ($appointments as $appointment) {
            $appointmentManager
                ->setExpert($appointment->getExpert())
                ->setAppointment($appointment)
                ->remind()
            ;
        }

        $output->writeln(implode(' ',$return));
    }
}
 