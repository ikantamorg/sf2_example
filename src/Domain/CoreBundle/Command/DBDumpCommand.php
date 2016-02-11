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



class DBDumpCommand extends Task
{
    protected function configure()
    {
        $this->setName('pdb:dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();

        $user = $container->getParameter('database_user');
        $db = $container->getParameter('database_name');
        $fileName = date('YmdHis').'.sql';

        $backupDir = realpath($container->get('kernel')->getRootDir().'/../backups/');
        $newBackupPath = $backupDir.'/'.$fileName;

        $command = "mysqldump -u {$user} '{$db}' > {$newBackupPath}";

        $data = shell_exec($command);

        $output->writeln('<info>'.$data.'</info>');
    }
}
