<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Deploy extends Base {

    protected function configure () {

        parent::configure();

        $this
            ->setName('deploy')
            ->setDescription('Deploy Command');

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

        $this->paths['env'] = $this->paths['root'] . '/deploy/' . $this->getEnv() . '/';
        $this->paths['environmentFile'] = $this->paths['env'] . 'environment.yml';

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        parent::execute($input, $output);

        $checkRevisionCmd = $this->getApplication()->find('check-revision');
        $sendCmd = $this->getApplication()->find('send');

        $checkRevisionArgs = array(
            'command' => 'check-revision',
            'env'     => $input->getArgument('env'),
        );
        $checkRevisionCmd->run(new ArrayInput($checkRevisionArgs), $output);

        $revision = Helper::getLocalRevision($this->paths['root']);

        $sendArgs = array(
            'command'  => 'send',
            'env'      => $input->getArgument('env'),
            'revision' => $revision,
        );
        $sendCmd->run(new ArrayInput($sendArgs), $output);

        $config = Yaml::parse($this->paths['environmentFile']);
        $cmdInstall =
            "ssh -t -i {$this->paths['env']}{$config['key']} {$config['user']}@{$config['host']} 'php {$config['dest_dir']}-{$revision}/deploy/deploy.php install {$this->getEnv()}'";

        passthru($cmdInstall);

    }

}