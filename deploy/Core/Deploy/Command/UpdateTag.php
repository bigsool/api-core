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

        $config = Yaml::parse(file_get_contents($this->paths['environmentFile']));

        $this->getOutput()->write(sprintf('Moving tag <info>%s</info>... ', $config['git_tag']));

        $this->updateTag($input, $output, $config, $this->paths['root']);

        $this->updateTag($input, $output, $config, $this->paths['root'].'/vendor/api/core');

        $this->getOutput()->writeln("OK\n");

        return 0;

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param                 $config
     */
    protected function updateTag (InputInterface $input, OutputInterface $output, $config, $dir) {

        $dir = escapeshellarg($dir);
        $cmd = "cd $dir; git tag -l | grep {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        $result = trim(`$cmd`);

        if ($result) {

            $cmd = "cd $dir; git tag -d {$config['git_tag']}";
            if ($input->getOption('verbose')) {
                $output->writeln(sprintf('<comment>%s</comment>', $cmd));
            }
            exec($cmd);

            $cmd = "cd $dir; git push origin :refs/tags/{$config['git_tag']}";
            if ($input->getOption('verbose')) {
                $output->writeln(sprintf('<comment>%s</comment>', $cmd));
            }
            exec($cmd);

        }

        $cmd = "cd $dir; git tag -f {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd);

        $cmd = "cd $dir; git push origin tag {$config['git_tag']}";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd);
    }

}