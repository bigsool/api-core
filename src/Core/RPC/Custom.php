<?php


namespace Core\RPC;


use Core\Action\Action;
use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Custom implements Local {

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
    protected $ipAddress;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string[]
     */
    protected $returnedFields;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $action;

    /**
     * @var string|null
     */
    protected $module;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var FormattedError
     */
    protected $error;

    /**
     * @return string
     */
    public function getService () {

        return $this->service;
    }

    /**
     * @param string $service
     *
     * @return Custom
     */
    public function setService ($service) {

        $this->service = $service;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod () {

        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return Custom
     */
    public function setMethod ($method) {

        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientName () {

        return $this->clientName;
    }

    /**
     * @param string $clientName
     *
     * @return Custom
     */
    public function setClientName ($clientName) {

        $this->clientName = $clientName;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientVersion () {

        return $this->clientVersion;
    }

    /**
     * @param string $clientVersion
     *
     * @return Custom
     */
    public function setClientVersion ($clientVersion) {

        $this->clientVersion = $clientVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale () {

        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Custom
     */
    public function setLocale ($locale) {

        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress () {

        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     *
     * @return Custom
     */
    public function setIpAddress ($ipAddress) {

        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath () {

        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Custom
     */
    public function setPath ($path) {

        $this->path = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return Custom
     */
    public function setParams ($params) {

        $this->params = $params;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getReturnedFields () {

        return $this->returnedFields;
    }

    /**
     * @param \string[] $returnedFields
     *
     * @return Custom
     */
    public function setReturnedFields ($returnedFields) {

        $this->returnedFields = $returnedFields;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getId () {

        return $this->id;
    }

    /**
     * @param null|string $id
     *
     * @return Custom
     */
    public function setId ($id) {

        $this->id = $id;

        return $this;
    }

    /**
     * @return string|Action
     */
    public function getAction () {

        return $this->action;
    }

    /**
     * @param string|Action $action
     *
     * @return Custom
     */
    public function setAction ($action) {

        $this->action = $action;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getModule () {

        return $this->module;
    }

    /**
     * @param null|string $module
     *
     * @return Custom
     */
    public function setModule ($module) {

        $this->module = $module;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult () {

        return $this->result;
    }

    /**
     * @param mixed $result
     *
     * @return Custom
     */
    public function setResult ($result) {

        $this->result = $result;

        return $this;
    }

    /**
     * @return FormattedError
     */
    public function getError () {

        return $this->error;
    }

    /**
     * @param FormattedError $error
     *
     * @return Custom
     */
    public function setError ($error) {

        $this->error = $error;

        return $this;
    }

    /**
     * @param Request $request
     */
    public function parse (Request $request) {
        // TODO: Implement parse() method.
    }

    /**
     * @return Response
     */
    public function getErrorResponse () {

        return NULL;

    }

    /**
     * @param Serializer $serializer
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer) {

        return NULL;

    }

    /**
     * @return array
     */
    public function getAuthToken () {

        return isset($this->params['authToken']) ? json_decode($this->params['authToken'], true) : [];

    }
}