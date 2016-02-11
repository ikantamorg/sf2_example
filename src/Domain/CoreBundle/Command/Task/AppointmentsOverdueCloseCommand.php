<?php
/**
 * User: alkuk
 * Date: 28.02.14
 * Time: 16:48
 */

namespace Domain\CoreBundle\Command\Task;

use Domain\CoreBundle\Command\Abstracts\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppointmentsOverdueCloseCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:overdue:close')
            ->setDescription("Close all overdue appointments.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $overdueAppointments = $container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CoreBundle:Appointment')
            ->getOverdue()
        ;

        $appointmentManager = $container->get('domain.appointment.manager');

        foreach ($overdueAppointments as $appointment) {
            $appointmentManager
                ->unsetActor()
                ->setAppointment($appointment)
                ->forceClose()
            ;
        }

        $count = count($overdueAppointments);

        $output->writeln(sprintf("%d overdue appointments were closed.", $count));
    }
}
