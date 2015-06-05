<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Send extends Base {

    protected $revOnTheServer;

    protected function configure () {

        parent::configure();

        $this
            ->setName('send')
            ->setDescription('Sending Files Command')
            ->addArgument('revision', InputArgument::REQUIRED, 'Revision to send');

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        $output->writeln('');

        parent::execute($input, $output);

        if (!$this->confirm(sprintf("About to send files to <env>%s</env>, OK ?\n[Y/n] ", $this->getEnv()))) {
            $this->abort('Install aborted by user');
        }

        $this->getOutput()->writeln("");

        $this->getCurrentRevisionOnTheServer();

        $this->copyPreviousRev();

        $this->rsync();

        return 0;

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

        $this->paths['rsync-excludeFile'] = $this->paths['root'] . '/deploy/rsync-exclude';

    }

    protected function getCurrentRevisionOnTheServer () {

        $this->getOutput()->write("Retrieving current revision on the server ... ");

        $this->revOnTheServer =
            Helper::getRevisionOnTheServer($this->getInput(), $this->getOutput(), $this->paths['env'],
                                           $this->paths['environmentFile']);
        if ($this->revOnTheServer == $this->getInput()->getArgument('revision')) {
            $this->abort(sprintf('Current server revision is already <rev>%s</rev>. Aborting', $this->revOnTheServer));
        }

        $this->getOutput()->writeln("OK\n");

        return $this->revOnTheServer;

    }

    protected function copyPreviousRev () {

        $shouldCopy = false;
        $revision = substr($this->getInput()->getArgument('revision'), 0, 7);
        if ($this->revOnTheServer) {

            if ($this->confirm(sprintf("Should we copy the previous revision folder <info>%s</info>\n" .
                                       "to the new revision folder <info>%s</info> ?\n[Y/n] ",
                                       $this->revOnTheServer, $revision))
            ) {
                $shouldCopy = true;
            }

        }

        if ($shouldCopy) {

            $config = Yaml::parse(file_get_contents($this->paths['environmentFile']));
            $remotePrevFolder =
                Helper::getRemoteDestLink($this->paths['environmentFile']) . '-' . $this->revOnTheServer;
            $remoteNextFolder = Helper::getRemoteDestLink($this->paths['environmentFile']) . '-' . $revision;

            $this->getOutput()->write("Copying previous revision folder ... ");

            $cmd = "ssh -i {$this->paths['env']}{$config['key']} {$config['user']}@{$config['host']} "
                   . "'cp -ra \"{$remotePrevFolder}\" \"{$remoteNextFolder}\"'";
            if ($this->getInput()->getOption('verbose')) {
                $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
            }
            system($cmd);

            $this->getOutput()->writeln("OK\n");

        }

    }

    protected function rsync () {

        $config = Yaml::parse(file_get_contents($this->paths['environmentFile']));

        $this->getOutput()->write("Sending files on the server ( it could take a while ) ... ");

        $cmd =
            'cd ' . $this->paths['env'] . ' && rsync -axv --delete --exclude-from="' . $this->paths['rsync-excludeFile']
            . "\" -e \"ssh -i {$config['key']}\" {$config['source_dir']} "
            . "{$config['user']}@{$config['host']}:{$config['dest_dir']}{$this->getEnv()}-"
            . $this->getInput()->getArgument('revision');
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        system($cmd);

        $this->getOutput()->writeln("OK\n");

    }

}