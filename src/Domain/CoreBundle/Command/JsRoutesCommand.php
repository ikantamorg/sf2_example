<?php
/**
 * User: dev
 * Date: 07.06.13
 * Time: 21:36
 */


namespace Domain\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JsRoutesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('js:routes:update')
            ->setDescription('Update js routes files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getApplication()->getKernel()->getEnvironment();

        $output->writeln('<comment>Update '.$env.' js file:</comment>');
        $this->runCommand(
            $output,
            'fos:js-routing:dump',
            [
                '--target' => 'web/js/routes/routes.'.$env.'.js'
            ]
        );

        $output->writeln('<comment>Done.</comment>');
    }


    protected function runCommand(OutputInterface $output, $command, array $params)
    {
        $command = $this->getApplication()->find($command);

        $arguments = array_merge(['command' => $command], $params);


        $new_input = new ArrayInput($arguments);
        $new_input->setInteractive(false);

        return $command->run($new_input, $output);

    }
}
