<?php
/**
 * User: Dred
 * Date: 18.12.13
 * Time: 14:14
 */

namespace Domain\CoreBundle\Command\Task;

use Domain\CoreBundle\Command\Abstracts\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class AppointmentPaymentCompleteCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:payment:complete')
            ->addArgument(
                'appointment_id',
                InputArgument::REQUIRED,
                'Appointment ID'
            )
            ->setDescription('Complete all appointments, that should be completed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $appointmentId = $input->getArgument('appointment_id');

        $appointment = $container->get('doctrine.orm.entity_manager')
            ->getRepository('CoreBundle:Appointment')
            ->find($appointmentId)
            ;

        if (!$appointment) {
            throw new Exception(sprintf("Appointment #%s to complete payment doesn't exist", $appointmentId));
        }

        $paymentService = $container->get('payment');

        if (!$paymentService->completeTransaction($appointment)) {
            throw new Exception(sprintf("Appointment #%s payment error: %s ", $appointmentId, $paymentService->getLastError()));
        }



        $output->writeln(sprintf("Appointment's #%s payment succesfully completed", $appointmentId));
    }
}
 