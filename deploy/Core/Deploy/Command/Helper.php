<?php

namespace Core\Deploy\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Helper {

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $envPath
     * @param string          $envFile
     *
     * @return string
     */
    public static function getRevisionOnTheServer (InputInterface $input, OutputInterface $output, $envPath, $envFile) {

        $config = Yaml::parse($envFile);
        $remoteDestLink = self::getRemoteDestLink($envFile);

        $cmd = "ssh -i {$envPath}{$config['key']} {$config['user']}@{$config['host']} "
               . "'ls -l \"{$remoteDestLink}\" 2> /dev/null'";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        $result = exec($cmd);

        return substr($result, strrpos($result, '-') + 1);

    }

    /**
     * @param string $envFile
     *
     * @return string
     */
    public static function getRemoteDestLink ($envFile) {

        $config = Yaml::parse($envFile);

        return $config['dest_dir'] . $config['env'];

    }

    /**
     * @param string $envFile
     * @param string $revision
     *
     * @return string
     */
    public static function getRemoteDestFolder ($envFile, $revision) {

        $config = Yaml::parse($envFile);

        $remoteDestFolder = self::getRemoteDestLink($envFile);

        return $remoteDestFolder . '-' . substr($revision, 0, 7);

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $rootPath
     * @param bool            $short
     *
     * @return string
     */
    public static function getLocalRevision (InputInterface $input, OutputInterface $output, $rootPath, $short = true) {

        $short = $short ? '--short' : '';

        $cmd = "cd {$rootPath} && git rev-parse {$short} HEAD";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }

        return trim(`{$cmd}`);

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $rootPath
     *
     * @return bool
     */
    public static function hasUncommittedFiles (InputInterface $input, OutputInterface $output, $rootPath) {

        $cmd = "cd {$rootPath} && git diff";
        if ($input->getOption('verbose')) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }

        return !!strlen(trim(`{$cmd}`));

    }

} 