<?php


namespace Core\Error;

use Core\Config\ConfigManager;
use Core\Context\ApplicationContext;
use Core\TestCase;

class ConfigManagerTest extends TestCase {

    private $yamlConfigPaths;

    public function setUp () {

        parent::setUp();

        $this->yamlConfigPaths = array(__DIR__ . '/config.yml');

    }

    public function testLoadConfigAndRoute () {

        $configManager = new ConfigManager($this->yamlConfigPaths);
        $this->assertInstanceOf('\Core\Config\ConfigManager', $configManager);
        $appCtx = ApplicationContext::getInstance();

    }

    public function testLoadEmpty () {

        $configManager = new ConfigManager($this->yamlConfigPaths);

        $method = new \ReflectionMethod($configManager, 'loadConfig');
        $method->setAccessible(true);
        $method->invokeArgs($configManager, array(array(__DIR__ . '/empty.yml')));

        $this->assertSame([], $configManager->getConfig());

    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigWithBadYamlFile () {

        $configManager = new ConfigManager($this->yamlConfigPaths);

        $meth = new \ReflectionMethod($configManager, 'loadConfig');
        $meth->setAccessible(true);
        $meth->invokeArgs($configManager, array(array(__DIR__ . '/configs.yml')));

    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigMalFormattedYamlFile () {

        $configManager = new ConfigManager($this->yamlConfigPaths);

        $meth = new \ReflectionMethod($configManager, 'loadConfig');
        $meth->setAccessible(true);
        $meth->invokeArgs($configManager, array(array(__DIR__ . '/malformatted.yml')));

    }

    /**
     * @expectedException \Exception
     */
    public function testLoadConfigMalFormatted2YamlFile () {

        $configManager = new ConfigManager($this->yamlConfigPaths);

        $meth = new \ReflectionMethod($configManager, 'loadConfig');
        $meth->setAccessible(true);
        $meth->invokeArgs($configManager, array(array(__DIR__ . '/malformatted2.yml')));

    }

    public function testGetConfig () {

        $configManager = new ConfigManager($this->yamlConfigPaths);


        $config = $configManager->getConfig();
        $this->assertTrue(is_array($config));

        $expectedConfig = [
            "server" => [
                "database" => [
                    "host"     => "http://archipad.com",
                    "name"     => "archiweb",
                    "user"     => "thierry",
                    "password" => "qweqwe"
                ]
            ]
        ];

        $this->assertEquals($expectedConfig, $config);

    }

}