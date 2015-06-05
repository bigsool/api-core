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
            ->setDescription('All in One command to do a complete deploy');

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        parent::execute($input, $output);

        $checkRevisionCmd = $this->getApplication()->find('check-revision');
        $sendCmd = $this->getApplication()->find('send');
        $updateTagCmd = $this->getApplication()->find('update-tag');

        $checkRevisionArgs = array(
            '-v'      => $input->getOption('verbose'),
            'command' => 'check-revision',
            'env'     => $input->getArgument('env'),
        );
        if ($errorCode = $checkRevisionCmd->run(new ArrayInput($checkRevisionArgs), $output)) {
            $this->abort($checkRevisionArgs['command'] . ' failed with error code: ' . $errorCode);
        }

        $revision = Helper::getLocalRevision($this->getInput(), $this->getOutput(), $this->paths['root']);

        $sendArgs = array(
            '-v'       => $input->getOption('verbose'),
            'command'  => 'send',
            'env'      => $input->getArgument('env'),
            'revision' => $revision,
        );
        if ($errorCode = $sendCmd->run(new ArrayInput($sendArgs), $output)) {
            $this->abort($sendArgs['command'] . ' failed with error code: ' . $errorCode);
        }

        $config = Yaml::parse(file_get_contents($this->paths['environmentFile']));
        $verboseOption = ($input->getOption('verbose')) ? ' -v ' : '';
        $cmdInstall =
            "ssh -t -i {$this->paths['env']}{$config['key']} {$config['user']}@{$config['host']} "
            . "'php {$config['dest_dir']}{$this->getEnv()}-{$revision}/deploy/deploy.php {$verboseOption} install {$this->getEnv()}'";

        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmdInstall));
        }
        passthru($cmdInstall, $errorCode);
        if ($errorCode) {
            $this->abort('install failed with error code: ' . $errorCode);
        }

        $updateTagArgs = array(
            '-v'      => $input->getOption('verbose'),
            'command' => 'update-tag',
            'env'     => $input->getArgument('env'),
        );
        if ($errorCode = $updateTagCmd->run(new ArrayInput($updateTagArgs), $output)) {
            $this->abort($updateTagArgs['command'] . ' failed with error code: ' . $errorCode);
        }

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

    }

}