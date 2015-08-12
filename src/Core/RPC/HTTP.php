<?php


namespace Core\RPC;


use Core\Context\ApplicationContext;
use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTP implements Handler {

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
    protected $service;

    /**
     * @var string
     */
    protected $method;

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
    protected $ipAddress;

    /**
     * @var string[]
     */
    protected $returnedFields;

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
     * @param FormattedError $error
     *
     * @return Response
     */
    public function getErrorResponse (FormattedError $error) {

        return new Response(strval($error), Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    /**
     * @return string
     */
    public function getIpAddress () {

        return $this->ipAddress;

    }

    /**
     * @return string
     */
    public function getLocale () {

        return $this->locale;

    }

    /**
     * @return string
     */
    public function getMethod () {

        return $this->method;

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
     * @return string
     */
    public function getService () {

        return $this->service;
    }

    /**
     * @param Serializer $serializer
     * @param mixed      $data
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer, $data) {

        return new Response(print_r($data, true), RESPONSE::HTTP_OK);

    }

    /**
     * @param Request $request
     *
     * @throws FormattedError
     */
    public function parse (Request $request) {

        $explodedPathInfo = explode('/', trim($request->getPathInfo(), '/'));

        if (!isset($explodedPathInfo[1])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_CLIENT_IS_INVALID);
        }
        $explodedClient = explode('+', $explodedPathInfo[1]);
        if (count($explodedClient) != 3) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_CLIENT_IS_INVALID);
        }
        list($this->clientName, $this->clientVersion, $this->locale) = $explodedClient;
        if ($this->locale != 'fr') {
            $this->locale = 'en';
        }

        if (!isset($explodedPathInfo[2])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_SERVICE_NOT_FOUND);
        }
        $this->service = $explodedPathInfo[2];

        if (!isset($explodedPathInfo[3])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_METHOD_NOT_FOUND);
        }
        $this->method = $explodedPathInfo[3];

        $this->path = '/' . $this->service . '/' . $this->method;

        $this->params = array_merge($request->query->all(), $request->request->all());

        $this->ipAddress = $request->getClientIp();

        $this->returnedRootEntity = NULL;
        $this->returnedFields = [];

    }

    /**
     * @return array
     */
    public function getAuthToken () {

        return isset($this->params['authToken']) && is_string($this->params['authToken']) ? json_decode($this->params['authToken'], true) : [];

    }
}