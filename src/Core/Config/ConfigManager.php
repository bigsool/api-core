<?php

namespace Core\Config;

use Core\Context\ApplicationContext;
use Core\Controller;
use Core\Util\ArrayExtra;
use Symfony\Component\Routing\Route;
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
     * @param string   $yamlRoutesPath
     */
    public function __construct ($yamlConfigPaths, $yamlRoutesPath) {

        $this->appCtx = ApplicationContext::getInstance();

        $this->loadConfig($yamlConfigPaths);
        $this->loadRoutes($yamlRoutesPath);

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
            $config = Yaml::parse($yamlFilePath);
            if (!is_array($config)) {
                throw new \RuntimeException('invalid config file');
                continue;
            }
            $configs = ArrayExtra::array_merge_recursive_distinct($configs, $config);
        }
        $this->config = $configs;

    }

    /**
     * @param $yamlRoutesPath
     *
     * @throws \Exception
     */
    private function loadRoutes ($yamlRoutesPath) {

        $parse = Yaml::parse($yamlRoutesPath);
        if (!is_array($parse)) {
            throw new \Exception('route yaml file can\'t be found');
        }
        foreach ($parse['routes'] as $name => $value) {
            $this->appCtx->addRoute($name, new Route($value['path'], [
                'controller' => new Controller(ApplicationContext::getInstance()->getProduct() . '\\'
                                               . $value['controller'], $value['method'])
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