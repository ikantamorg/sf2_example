<?php
/**
 * User: Dred
 * Date: 18.12.13
 * Time: 14:14
 */

namespace Domain\CoreBundle\Command\Task;

use Domain\CoreBundle\Command\Abstracts\Task;
use Domain\CoreBundle\Entity\Appointment;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Domain\CoreBundle\Util\Resque;

/**
 * Class AppointmentVideoStatusUpdateCommand
 */
class AppointmentVideoStatusUpdateCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:appointment:video:status:update')
            ->addArgument(
                'video_path',
                InputArgument::REQUIRED,
                'Path to video'
            )
            ->setDescription("Update video's status.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $videoPath = $input->getArgument('video_path');
        $realVideoPath = realpath($videoPath);

        if (!$realVideoPath) {
            throw new Exception(sprintf("Appointments' video #%s doesn't exist", $videoPath));
        }


        $fileName = pathinfo($realVideoPath);

        $em = $container->get('doctrine.orm.entity_manager');

        $appointment = $em
            ->getRepository('CoreBundle:Appointment')
            ->findOneBySessionId($fileName['filename'])
            ;

        if (!$appointment) {
            throw new Exception(sprintf("Appointment #%s to complete video status doesn't exist", $fileName['filename']));
        }

        $appointmentManager = $container->get('domain.appointment.manager');
        $appointmentManager
            ->unsetActor()
            ->setAppointment($appointment)
            ->completeVideoProcessing()
        ;



        $output->writeln(sprintf("Appointment's #%s video status updated", $appointment->getId()));
    }
}
 