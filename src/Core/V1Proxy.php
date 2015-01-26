<?php


namespace Core;

use Core\Context\ApplicationContext;

class V1Proxy {

    /**
     * @var array
     */
    protected $config;

    public function __construct () {

        $this->config = ApplicationContext::getInstance()->getConfigManager()->getConfig()['v1'];

        require_once ROOT_DIR . '/' . $this->config['path'] . '/archiweb/include/lib/dispatcher/localDispatcher.php';

    }

    /**
     * @param $service
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function call ($service, $method, $params) {

        return callLocalAPI($service, $method, $params)->getResult();

    }

}