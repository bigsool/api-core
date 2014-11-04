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
     * @var string
     */
    protected $returnedRootEntity;

    /**
     * @var string[]
     */
    protected $returnedFields;

    /**
     * @param Request $request
     *
     * @throws \Archiweb\Error\FormattedError
     */
    public function __construct (Request $request) {

        $explodedPathInfo = explode('/', trim($request->getPathInfo(), '/'));

        if (!isset($explodedPathInfo[1])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_CLIENT_IS_INVALID);
        }
        $explodedClient = explode('+', $explodedPathInfo[1]);
        if (count($explodedClient) != 3) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_CLIENT_IS_INVALID);
        }
        list($this->clientName, $this->clientVersion, $this->locale) = $explodedClient;
        if ($this->locale != 'fr') {
            $this->locale = 'en';
        }

        if (!isset($explodedPathInfo[2])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_SERVICE_NOT_FOUND);
        }
        $service = $explodedPathInfo[2];

        $method = $request->query->get('method');
        if (!isset($method) || !is_string($method)) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_METHOD_NOT_FOUND);
        }

        $this->path = '/' . $service . '/' . $method;

        $this->params = $request->query->get('params') ?: [];

        $this->returnedRootEntity = $request->query->get('entity');
        $this->setReturnedFields($request->query->get('fields'));

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

    /**
     * @return string[]
     */
    public function getReturnedFields () {

        return $this->returnedFields;

    }

    /**
     * @param string[] $fields
     *
     * @throws \Archiweb\Error\FormattedError
     */
    protected function setReturnedFields (array $fields = NULL) {

        $fields = (array)$fields;
        foreach ($fields as $field) {
            if (!is_string($field) || $field == '*') {
                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_BAD_FIELD);
            }
        }

        $this->returnedFields = $fields;

    }

    /**
     * @return string
     */
    public function getReturnedRootEntity () {

        return $this->returnedRootEntity;

    }
}