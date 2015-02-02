<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Send extends Base {

    protected function configure () {

        parent::configure();

        $this
            ->setName('send')
            ->setDescription('Sending Files Command')
            ->addArgument('revision', InputArgument::REQUIRED, 'Revision to send');

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

        $this->paths['env'] = $this->paths['root'] . '/' . $this->getEnv() . '/';
        $this->paths['environmentFile'] = $this->paths['env'] . 'environment.yml';
        $this->paths['rsync-excludeFile'] = $this->paths['env'] . 'rsync-exclude';

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        $output->writeln('');

        parent::execute($input, $output);

        if (!$this->confirm(sprintf("About to send files to <env>%s</env>, OK ?\n[Y/n] ", $this->getEnv()))) {
            $this->abort('Install aborted by user');
        }

        $this->getOutput()->writeln("");

        $this->getCurrentRevisionOnTheServer();

        $this->rsync();

    }

    protected function getCurrentRevisionOnTheServer () {

        $this->getOutput()->write("Retrieving current revision on the server ... ");

        $revision = Helper::getRevisionOnTheServer($this->paths['env'], $this->paths['environmentFile']);
        if ($revision == $this->getInput()->getArgument('revision')) {
            $this->abort(sprintf('Current server revision is already <rev>%s</rev>. Aborting', $revision));
        }

        $this->getOutput()->writeln("OK\n");

        return $revision;

    }

    protected function rsync () {

        $config = Yaml::parse($this->paths['environmentFile']);

        $this->getOutput()->write("Sending files on the server ( it could take a while ) ... ");

        $cmd =
            'cd ' . $this->paths['env'] . ' && rsync -axv --delete --exclude-from="' . $this->paths['rsync-excludeFile']
            . "\" -e \"ssh -i {$config['key']}\" {$config['source_dir']} {$config['user']}@{$config['host']}:{$config['dest_dir']}-"
            . $this->getInput()->getArgument('revision');

        system($cmd);

        $this->getOutput()->writeln("OK\n");

    }

}