<?php


namespace Archiweb\RPC;


use Archiweb\Context\ApplicationContext;
use Symfony\Component\HttpFoundation\Request;

class JSONP implements Handler {

    /**
     * @var string
     */
    protected $clientName;

    /**
     * @var string
     */
    protected $clientVersion;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param ApplicationContext $context
     * @param Request            $request
     */
    public function __construct (ApplicationContext $context, Request $request) {

        $explodedPathInfo = explode('/', trim($request->getPathInfo(), '/'));

        if (!isset($explodedPathInfo[1])) {
            // TODO: use the ErrorManager
            throw new \RuntimeException('invalid client');
        }
        $explodedClient = explode('+', $explodedPathInfo[1]);
        if (count($explodedClient) != 3) {
            // TODO: use the ErrorManager
            throw new \RuntimeException('invalid client');
        }
        list($this->clientName, $this->clientVersion, $this->locale) = $explodedClient;
        if ($this->locale != 'fr') {
            $this->locale = 'en';
        }

        if (!isset($explodedPathInfo[2])) {
            // TODO: use the ErrorManager
            throw new \RuntimeException('invalid service');
        }
        $service = $explodedPathInfo[2];

        $method = $request->query->get('method');
        if (!isset($method) || !is_string($method)) {
            // TODO: use the ErrorManager
            throw new \RuntimeException('invalid method');
        }

        $this->path = '/' . $service . '/' . $method;

        $this->params = $request->query->get('params') ?: [];

    }

    /**
     * @return string
     */
    public function getClientName () {

        return $this->clientName;

    }

    /**
     * @return string
     */
    public function getClientVersion () {

        return $this->clientVersion;

    }

    /**
     * @return string
     */
    public function getLocale () {

        return $this->locale;

    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @return string
     */
    public function getPath () {

        return $this->path;

    }
}