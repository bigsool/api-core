<?php


namespace Core\Deploy\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateTag extends Base {

    protected function configure () {

        parent::configure();

        $this
            ->setName('update-tag')
            ->setDescription('Update Git tag');

    }

    protected function execute (InputInterface $input, OutputInterface $output) {

        parent::execute($input, $output);

        $config = Yaml::parse($this->paths['environmentFile']);

        $this->getOutput()->write(sprintf('Moving tag <info>%s</info>... ', $config['git_tag']));

        $cmd = "git tag -l | grep {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        $result = trim(`$cmd`);

        if ($result) {

            $cmd = "git tag -d {$config['git_tag']}";
            if ($input->getOption('verbose')) {
                $output->writeln(sprintf('<comment>%s</comment>', $cmd));
            }
            exec($cmd);

            $cmd = "git push origin :refs/tags/{$config['git_tag']}";
            if ($input->getOption('verbose')) {
                $output->writeln(sprintf('<comment>%s</comment>', $cmd));
            }
            exec($cmd);

        }

        $cmd = "git tag -f {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd);

        $cmd = "git push origin tag {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd);

        $this->getOutput()->writeln("OK\n");

    }

}