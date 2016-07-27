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
     * @param string[] $yamlFilePaths
     *
     * @throws \Exception
     */
    private function loadConfig (array $yamlFilePaths) {

        $key = 'CONFIG_' . md5(serialize($yamlFilePaths));

        $cacheProvider = $this->appCtx->getCacheProvider();
        $configs = $cacheProvider->fetch($key);

        if ($configs === false) {

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
                $configs = ArrayExtra::array_merge_recursive_distinct($configs, $config);
            }

            // trade-off, if we change the config without clearing the cache, it will take up to 10m to be updated
            $cacheProvider->save($key, $configs, 600);
        }

        // replace ENV:xxxx by Environment value
        array_walk_recursive($configs, function (&$item, $key) {

            if (is_string($item) && strpos($item, 'ENV:', 0) === 0) {
                $envVarName = substr($item, 4);
                $item = getenv($envVarName);
            }
        });

        $this->config = $configs;

    }

    /**
     * @return array
     */
    public function getConfig () {

        return $this->config;

    }

}
