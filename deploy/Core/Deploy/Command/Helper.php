<?php

namespace Core\Deploy\Command;


use Symfony\Component\Yaml\Yaml;

class Helper {

    public static function getRevisionOnTheServer ($envPath, $envFile) {

        $config = Yaml::parse($envFile);

        $cmd =
            "ssh -i {$envPath}{$config['key']} {$config['user']}@{$config['host']} 'ls -la \"{$config['dest_dir']}\" 2> /dev/null'";
        $result = exec($cmd);

        return substr($result, strrpos($result, '-') + 1);
    }

    public static function getLocalRevision ($rootPath, $short = true) {

        $short = $short ? '--short' : '';

        return trim(`cd {$rootPath} && git rev-parse {$short} HEAD`);
    }

} 