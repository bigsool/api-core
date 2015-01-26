<?php


namespace Core;

use Core\Context\ApplicationContext;
use Core\Error\FormattedError;

class V1Proxy {

    /**
     * @var array
     */
    protected $config;

    public function __construct () {

        $this->config = ApplicationContext::getInstance()->getConfigManager()->getConfig()['v1'];

        require_once ROOT_DIR . '/' . $this->config['path'] . '/include/lib/dispatcher/localDispatcher.php';

    }

    /**
     * @param $service
     * @param $method
     * @param $params
     *
     * @return mixed
     * @throws FormattedError
     */
    public function call ($service, $method, $params) {

        try {
            return callLocalAPI($service, $method, $params)->getResult();
        } catch (\ArchiwebException $e) {
            throw new FormattedError($e->getErrorArray());
        }

    }

}