<?php

namespace Archiweb\Config;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

class ConfigManager  {

    /**
     * @var ConfigValidator
     */
    private $configValidator;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $yamlFilePaths;

    /**
     * @param string $yamlFilePaths
     */
    function __construct ($yamlFilePaths) {

        $this->configValidator = new ConfigValidator();
        $this->configValidator->setConfigValidations();
        $this->yamlFilePaths = $yamlFilePaths;
        $this->loadConfig();

    }

    private function loadConfig () {

        $configs = [];
        foreach ($this->yamlFilePaths as $yamlFilePath) {
            $configs = array_merge($configs, Yaml::parse($yamlFilePath));
        }
        $processor = new Processor();
        $this->config = $processor->processConfiguration(
            $this->configValidator,
            $configs
        );

    }

    /**
     * @return array
     */
    public function getConfig () {

        return $this->config;

    }


}

?>