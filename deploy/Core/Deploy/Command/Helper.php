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

        $cmd =
            "ssh -i {$envPath}{$config['key']} {$config['user']}@{$config['host']} 'ls -la \"{$config['dest_dir']}\" 2> /dev/null'";
        $result = exec($cmd);

        return substr($result, strrpos($result, '-') + 1);

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