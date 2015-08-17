<?php


namespace Core\RPC;


use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CLI implements Handler {

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
     * @param Serializer $serializer
     * @param mixed      $data
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer, $data) {

        return new Response(json_encode(['jsonrpc' => '2.0',
                                         'result'  => $serializer->serialize($data)->get(),
                                         'id'      => $this->getId(),
                                        ]),
                            Response::HTTP_OK, [
                                'Content-type'                => 'application/json',
                                'Access-Control-Allow-Origin' => '*'
                            ]);

    }

    /**
     * @param FormattedError $error
     *
     * @return Response
     */
    public function getErrorResponse (FormattedError $error) {

        return new Response(json_encode(['jsonrpc' => '2.0',
                                         'error'   => $error->toArray(),
                                         'id'      => $this->getId(),
                                        ]),
                            Response::HTTP_OK, [
                                'Content-type'                => 'application/json',
                                'Access-Control-Allow-Origin' => '*'
                            ]);

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
     * @param string[] $fields
     *
     * @throws \Core\Error\FormattedError
     */
    protected function setReturnedFields (array $fields = NULL) {

        $fields = (array)$fields;

        $this->returnedFields = $fields;

    }

    /**
     * @return string
     */
    public function getService () {

        return $this->service;

    }

    /**
     * @param Request $request
     *
     * @throws \Core\Error\FormattedError
     */
    public function parse (Request $request) {

        global $argv;

        $this->module = $argv[1];
        $this->action = $argv[2];
        $this->params = [];
        $this->returnedFields = [];
        $this->ipAddress = 'localhost';

    }

    /**
     * @return null|string
     */
    public function getId () {

        return $this->id;

    }

    /**
     * @return array
     */
    public function getAuthToken () {

        return isset($this->params['authToken']) ? json_decode($this->params['authToken'], true) : [];

    }

    public function getAction() {

        return $this->action;

    }

    public function getModule() {

        return $this->module;

    }

}