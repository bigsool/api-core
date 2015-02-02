<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class CheckRevision extends Base {

    protected $revision;

    protected function configure () {

        parent::configure();

        $this
            ->setName('check-revision')
            ->setDescription('Checking Revision Command');

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

        $this->paths['env'] = $this->paths['root'] . '/' . $this->getEnv() . '/';
        $this->paths['environmentFile'] = $this->paths['env'] . 'environment.yml';

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        $output->writeln('');

        parent::execute($input, $output);

        $this->areYouSureYouWantToContinue();

        $revision = $this->checkRevision();

        $this->checkIfRevisionAlreadyPushed($revision);

        $this->checkCurrentRevision($revision);

    }

    protected function areYouSureYouWantToContinue () {

        $this->getOutput()->writeln(sprintf("------ WARNING"));
        $this->getOutput()->writeln(sprintf("------ THIS SCRIPT WILL PUSH THE SPECIFIED FOLDER TO <env>%s</env>",
                                            strtoupper($this->getEnv())));
        if (!$this->confirm("------ ARE YOU SURE YOU WANT TO CONTINUE ?\n[Y/n] ")) {
            $this->abort('Checking revision aborted by user');
        }

        $this->getOutput()->writeln('');

    }

    protected function checkRevision () {

        $revision =
            $this->getQuestion()->ask($this->getInput(), $this->getOutput(),
                                      new Question(sprintf("------ Please enter the revision to push to <env>%s</env>\n[e.g. bc2e1f3] ",
                                                           $this->getEnv())));

        if (strlen($revision) < 7) {
            $this->abort("------ ERROR : Not a valid revision");
        }

        $currentRevision = substr(Helper::getLocalRevision($this->paths['root'], false), 0, strlen($revision));

        if ($currentRevision != $revision) {
            $this->getOutput()->writeln(sprintf("\n\n------ WARNING"));
            $this->getOutput()
                 ->writeln(sprintf("------ Requested revision doesn't match source revision (<rev>%s</rev> != <rev>%s</rev>)",
                                   $revision, $currentRevision));
            if (!$this->confirm("------ Are you sure you want to continue ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }
        }

        $this->getOutput()->writeln('');

        return $revision;

    }

    protected function checkIfRevisionAlreadyPushed ($revision) {

        $config = Yaml::parse($this->paths['environmentFile']);
        $completeOutputPath = $config['dest_dir'] . '-' . $revision;
        $cmd =
            "ssh -i {$this->paths['env']}{$config['key']} {$config['user']}@{$config['host']} 'ls \"{$completeOutputPath}\" 2> /dev/null'";

        $result = exec($cmd);

        $fileExists = $result != '';

        if ($fileExists) {

            $this->getOutput()->writeln(sprintf("------ WARNING"));
            $this->getOutput()->writeln(sprintf("------ The remote folder <info>%s</info> exists !",
                                                $completeOutputPath));
            $this->getOutput()
                 ->writeln(sprintf("<options=bold;fg=magenta>You are pretty likely doing something stupid.</option=bold;fg=magenta>"));
            if (!$this->confirm("------ Are you sure you want to continue ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }

            $this->getOutput()->writeln('');

        }

    }

    protected function checkCurrentRevision ($revision) {

        $currentRev = Helper::getRevisionOnTheServer($this->paths['env'], $this->paths['environmentFile']);

        if (strlen($currentRev) == 0) {
            $this->getOutput()
                 ->writeln("<options=bold;fg=magenta>No revision found on server.</options=bold;fg=magenta>");
            if (!$this->confirm("Is It your first commit ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }
            $this->getOutput()->writeln('');
        }
        elseif (strlen($currentRev) != 7) {
            $this->abort(sprintf("------ ERROR : Not a valid revision found on server: <rev>%s</rev>", $currentRev));
        }
        else {
            $this->getOutput()->writeln(sprintf("Revision found on server: <rev>%s</rev>", $currentRev));
        }

        if ($currentRev == $revision) {
            $this->abort(sprintf("------ ERROR : Requested rev <rev>%s</rev> is same as current server revision <rev>%s</rev>",
                                 $revision, $currentRev));
        }

    }

}