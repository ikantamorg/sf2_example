<?php
/**
 * User: Dred
 * Date: 18.12.13
 * Time: 10:12
 */

namespace Domain\CoreBundle\Command\Task;

use Domain\CoreBundle\Command\Abstracts\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppointmentCompleteCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:complete')->setDescription('Complete all appointments, that should be completed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $appointmentRepository = $container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CoreBundle:Appointment');

        $appointmentManager = $container->get('domain.appointment.manager');
        $appointmentManager->unsetActor();


        //retrieve appointments for completion
        $appointments = $appointmentRepository->getForCompletion();


        $return = count($appointments).' - appointments for completion.';

        foreach ($appointments as $appointment) {
            $appointmentManager
                ->setAppointment($appointment)
                ->complete()
            ;
        }

        $output->writeln($return);
    }
}
 