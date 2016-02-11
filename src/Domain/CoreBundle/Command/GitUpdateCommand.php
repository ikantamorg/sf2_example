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

class GitUpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('git:update')
            ->setDescription('Run updates after git pull')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Install assets:</comment>');
        $this->runCommand($output, 'assets:install', ['--symlink' => true]);

        $output->writeln('<comment>Run schema update:</comment>');
        $this->runCommand($output, 'doctrine:schema:update', ['--force' => true]);

        $output->writeln('<comment>Update routes:</comment>');
        $this->runCommand($output, 'js:routes:update', []);

        $output->writeln('<comment>Done.</comment>');
    }


    protected function runCommand(OutputInterface $output, $command, array $params){
        $command = $this->getApplication()->find($command);

        $arguments = array_merge(['command' => $command], $params);


        $new_input = new ArrayInput($arguments);
        $new_input->setInteractive(false);

        return $command->run($new_input, $output);

    }


}

