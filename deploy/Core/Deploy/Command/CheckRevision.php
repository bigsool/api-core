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

    protected function execute (InputInterface $input, OutputInterface $output) {

        $output->writeln('');

        parent::execute($input, $output);

        $this->areYouSureYouWantToContinue();

        $this->checkIfUncommittedChangesExists();

        $revision = $this->checkRevision();

        $this->checkIfRevisionAlreadyPushed($revision);

        $this->checkCurrentRevision($revision);

        return 0;

    }

    protected function setEnv ($env) {

        parent::setEnv($env);

    }

    protected function areYouSureYouWantToContinue () {

        $this->getOutput()->writeln(sprintf("This script will push the specified folder to <env>%s</env>",
                                            strtoupper($this->getEnv())));
        if (!$this->confirm("Are you sure you want to continue ?\n[Y/n] ")) {
            $this->abort('Checking revision aborted by user');
        }

        $this->getOutput()->writeln('');

    }

    protected function checkIfUncommittedChangesExists () {

        if (Helper::hasUncommittedFiles($this->getInput(), $this->getOutput(), $this->paths['root'])) {

            $this->getOutput()->writeln(sprintf("<warning>UNCOMMITTED CHANGES WILL BE PUSHED</warning>",
                                                strtoupper($this->getEnv())));
            if (!$this->confirm("<warning>ARE YOU SURE YOU WANT TO CONTINUE ?\n[Y/n] </warning>")) {
                $this->abort('Checking revision aborted by user');
            }

            $this->getOutput()->writeln('');

        }

    }

    protected function checkRevision () {

        $revision =
            $this->getQuestion()->ask($this->getInput(), $this->getOutput(),
                                      new Question(sprintf("Please enter the revision to push to <env>%s</env>\n[e.g. bc2e1f3] ",
                                                           $this->getEnv())));

        if (strlen($revision) < 7) {
            $this->abort("ERROR : Not a valid revision");
        }

        $currentRevision =
            substr(Helper::getLocalRevision($this->getInput(), $this->getOutput(), $this->paths['root'], false), 0,
                   strlen($revision));

        if ($currentRevision != $revision) {
            $this->getOutput()->writeln(sprintf("\n\nWARNING"));
            $this->getOutput()
                 ->writeln(sprintf("Requested revision doesn't match source revision (<rev>%s</rev> != <rev>%s</rev>)",
                                   $revision, $currentRevision));
            if (!$this->confirm("Are you sure you want to continue ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }
        }

        $this->getOutput()->writeln('');

        return $revision;

    }

    protected function checkIfRevisionAlreadyPushed ($revision) {

        $config = Yaml::parse($this->paths['environmentFile']);
        $completeOutputPath = $config['dest_dir'] . $this->getEnv() . '-' . substr($revision, 0, 7);
        $cmd = "ssh -i {$this->paths['env']}{$config['key']} {$config['user']}@{$config['host']} "
               . "'ls \"{$completeOutputPath}\" 2> /dev/null'";

        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        $result = exec($cmd);

        $fileExists = $result != '';

        if ($fileExists) {

            $this->getOutput()->writeln(sprintf("<warning>WARNING</warning>"));
            $this->getOutput()->writeln(sprintf("<warning>The remote folder <info>%s</info> exists !</warning>",
                                                $completeOutputPath));
            $this->getOutput()
                 ->writeln(sprintf("<warning>You are pretty likely doing something stupid.</warning>"));
            if (!$this->confirm("Are you sure you want to continue ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }

            $this->getOutput()->writeln('');

        }

    }

    protected function checkCurrentRevision ($revision) {

        $currentRev =
            Helper::getRevisionOnTheServer($this->getInput(), $this->getOutput(), $this->paths['env'],
                                           $this->paths['environmentFile']);

        if (strlen($currentRev) == 0) {
            $this->getOutput()
                 ->writeln("<warning>No revision found on server.</warning>");
            if (!$this->confirm("Is it your first commit ?\n[Y/n] ")) {
                $this->abort('Checking revision aborted by user');
            }
            $this->getOutput()->writeln('');
        }
        elseif (strlen($currentRev) != 7) {
            $this->abort(sprintf("ERROR : Not a valid revision found on server: <rev>%s</rev>", $currentRev));
        }
        else {
            $this->getOutput()->writeln(sprintf("Revision found on server: <rev>%s</rev>", $currentRev));
        }

        if ($currentRev == $revision) {
            $this->abort(sprintf("ERROR : Requested rev <rev>%s</rev> is same as current server revision <rev>%s</rev>",
                                 $revision, $currentRev));
        }

    }

}