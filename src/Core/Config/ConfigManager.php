<?php

namespace Core\Config;

use Core\Context\ApplicationContext;
use Core\Util\ArrayExtra;
use Symfony\Component\Yaml\Yaml;

class ConfigManager {

    /**
     * @var ApplicationContext
     */
    private $appCtx;

    /**
     * @var array
     */
    private $config;

    /**
     * @param string[] $yamlConfigPaths
     *
     * @throws \Exception
     */
    public function __construct ($yamlConfigPaths) {

        $this->appCtx = ApplicationContext::getInstance();

        $this->loadConfig($yamlConfigPaths);

    }

    /**
     * @param $yamlFilePaths
     *
     * @throws \Exception
     */
    private function loadConfig ($yamlFilePaths) {

        $configs = [];
        foreach ($yamlFilePaths as $yamlFilePath) {
            if (!file_exists($yamlFilePath)) {
                throw new \RuntimeException('config file not found');
            }
            if (!filesize($yamlFilePath)) {
                // don't try to load empty file
                continue;
            }
            $config = Yaml::parse(file_get_contents($yamlFilePath));
            if (!is_array($config)) {
                throw new \RuntimeException('invalid config file');
            }
            if (basename($yamlFilePath) == 'env.yml') {
                array_walk_recursive($config, function(&$varName){
                    $varName = getenv($varName);
                });
            }
            $configs = ArrayExtra::array_merge_recursive_distinct($configs, $config);
        }
        $this->config = $configs;

    }

    /**
     * @return array
     */
    public function getConfig () {

        return $this->config;

    }

}