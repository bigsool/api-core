<?php


namespace Archiweb\Error;

use Archiweb\TestCase;
use \Archiweb\Config\ConfigManager;

class ConfigManagerTest extends TestCase {

    private $yamlConfigPaths;

    private $yamlRoutesPath;

    public function setUp () {

        parent::setUp();

        $this->yamlConfigPaths = array(__DIR__.'/config.yml');
        $this->yamlRoutesPath = __DIR__.'/routes.yml';

    }

    public function testLoadConfigAndRoute () {

        $configManager = new ConfigManager($this->yamlConfigPaths, $this->yamlRoutesPath);
        $this->assertInstanceOf('\Archiweb\Config\ConfigManager',$configManager);

    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigWithBadYamlFile() {

        $configManager = new ConfigManager($this->yamlConfigPaths, $this->yamlRoutesPath);

        $meth = new \ReflectionMethod($configManager, 'loadConfig');
        $meth->setAccessible(true);
        $meth->invokeArgs($configManager, array(array(__DIR__.'/configs.yml')));

    }

    /**
     * @expectedException \Exception
     */
    public function testLoadRouteWithBadYamlFile () {

        $configManager = new ConfigManager($this->yamlConfigPaths, $this->yamlRoutesPath);

        $meth = new \ReflectionMethod($configManager, 'loadRoutes');
        $meth->setAccessible(true);
        $meth->invokeArgs($configManager, array(__DIR__.'/routess.yml'));

    }

    public function testGetConfig () {

        $configManager = new ConfigManager($this->yamlConfigPaths, $this->yamlRoutesPath);


        $config = $configManager->getConfig();
        $this->assertTrue(is_array($config));

        $expectedConfig = [
            "server" => [
                "database" => [
                    "host" => "http://archipad.com",
                    "name" => "archiweb",
                    "user"=> "thierry",
                    "password"=> "qweqwe"
                ]
            ]
        ];

        $this->assertEquals($expectedConfig,$config);

    }


}