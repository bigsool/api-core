<?php

namespace Core\Deploy\Command;


use Symfony\Component\Yaml\Yaml;

class Helper {

    /**
     * @param string $envPath
     * @param string $envFile
     *
     * @return string
     */
    public static function getRevisionOnTheServer ($envPath, $envFile) {

        $config = Yaml::parse($envFile);
        $remoteDestLink = self::getRemoteDestLink($envFile);

        $cmd = "ssh -i {$envPath}{$config['key']} {$config['user']}@{$config['host']} "
               . "'ls -l \"{$remoteDestLink}\" 2> /dev/null'";
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
     * @param string $rootPath
     * @param bool   $short
     *
     * @return string
     */
    public static function getLocalRevision ($rootPath, $short = true) {

        $short = $short ? '--short' : '';

        return trim(`cd {$rootPath} && git rev-parse {$short} HEAD`);

    }

    /**
     * @param string $rootPath
     *
     * @return bool
     */
    public static function hasUncommittedFiles ($rootPath) {

        return !!strlen(trim(`cd {$rootPath} && git diff`));

    }

} 