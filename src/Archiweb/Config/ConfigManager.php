<?php

namespace Archiweb\Config;

use Archiweb\Context\ApplicationContext;
use Archiweb\Controller;
use Symfony\Component\Routing\Route;
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
     * @param string $yamlFilePaths
     */
    function __construct ($yamlConfigPaths, $yamlRoutesPath) {

        $this->appCtx = ApplicationContext::getInstance();
        $this->configValidator = new ConfigValidator();
        $this->configValidator->setConfigValidations();

        $this->loadConfig($yamlConfigPaths);
        $this->loadRoutes($yamlRoutesPath);

    }

    private function loadConfig ($yamlFilePaths) {

        $configs = [];
        foreach ($yamlFilePaths as $yamlFilePath) {
            $configs = array_merge($configs, Yaml::parse($yamlFilePath));
        }
        $processor = new Processor();
        $this->config = $processor->processConfiguration(
            $this->configValidator,
            $configs
        );

    }

    private function loadRoutes ($yamlRoutesPath) {

        $parse = Yaml::parse($yamlRoutesPath);
        foreach($parse['routes'] as $name => $value) {
            $this->appCtx->addRoute($name, new Route($value['path'], [
                'controller' => new Controller($value['controller'], $value['method'])
            ]));
        }

    }

    /**
     * @return array
     */
    public function getConfig () {

        return $this->config;

    }


}

?>