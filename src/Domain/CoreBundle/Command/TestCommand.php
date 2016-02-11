<?php
/**
 * User: Dred
 * Date: 16.12.13
 * Time: 14:36
 */

namespace Domain\CoreBundle\Command;

use Domain\CoreBundle\Command\Abstracts\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Codeception\Util\Stub;


/**
 * Class TestCommand
 */
class TestCommand extends Task
{
    protected function configure()
    {
        $this->setName('task:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $templating = $this->getContainer()->get("templating");

        $template = 'CoreBundle:Emails:Appointment/onExpertWriteFeedback.html.twig';

        $appointment = Stub::makeEmpty(
            'Domain\CoreBundle\Entity\Appointment',
            array(
                'getId' => function () { return 1; }
            )
        );

        $candidate = Stub::makeEmpty(
            'Domain\CoreBundle\Entity\User',
            array(
                'getFullName' => function () { return 'alex'; }
            )
        );


        $data = $templating->render($template, array(
            'appointment' => $appointment,
            'candidate' => $candidate,
        ));

        $output->writeln('<info>'.$data.'</info>');
    }
}
