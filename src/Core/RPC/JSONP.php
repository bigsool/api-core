<?php


namespace Core\RPC;


use Core\Context\ApplicationContext;
use Core\Serializer;
use Core\Util\ArrayExtra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JSONP extends Handler {

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
    protected $callback;

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
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer) {

        return new Response($this->getCallback() . '(' . json_encode(
                                [
                                    'jsonrpc' => '2.0',
                                    'result'  => ['success' => true,
                                                  'data'    => $serializer->serialize($this->getResult())->get()
                                    ],
                                    'id'      => $this->getId(),
                                ]) . ')',
                            Response::HTTP_OK);

    }

    /**
     * @return Response
     */
    public function getErrorResponse () {

        return new Response($this->getCallback() . '(' . json_encode(['jsonrpc' => '2.0',
                                                                      'error'   => $this->getError()->toArray(),
                                                                      'id'      => $this->getId(),
                                                                     ]) . ')',
                            Response::HTTP_OK);

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

        $explodedPathInfo = explode('/', trim($request->getPathInfo(), '/'));

        if (!isset($explodedPathInfo[1])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_CLIENT_IS_INVALID);
        }
        $explodedClient = explode('+', $explodedPathInfo[1]);
        if (count($explodedClient) != 3) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_CLIENT_IS_INVALID);
        }
        list($this->clientName, $this->clientVersion, $this->locale) = $explodedClient;

        if (!isset($explodedPathInfo[2])) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_SERVICE_NOT_FOUND);
        }
        $this->service = $explodedPathInfo[2];

        $JSONContent = json_decode($request->getContent(), true) ?: [];

        $this->method = isset($JSONContent['method']) ? $JSONContent['method'] : $request->query->get('method');
        if (!isset($this->method) || !is_string($this->method)) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_METHOD_NOT_FOUND);
        }

        $this->callback = isset($JSONContent['callback']) ? $JSONContent['callback'] : $request->query->get('callback');

        $this->path = '/' . $this->service . '/' . $this->method;

        $getParams = json_decode($request->query->get('params'), true) ?: [];
        $jsonParams = isset($JSONContent['params']) && is_array($JSONContent['params']) ? $JSONContent['params'] : [];
        $cookies = $request->cookies->all();
        $this->params = ArrayExtra::array_merge_recursive_distinct($cookies, $getParams, $jsonParams);

        $this->id = isset($JSONContent['id']) ? $JSONContent['id'] : $request->query->get('id');

        $this->ipAddress = $request->getClientIp();

        $this->setReturnedFields(isset($JSONContent['fields']) ? $JSONContent['fields']
                                     : $request->query->get('fields'));

    }

    /**
     * @return string
     */
    public function getCallback () {

        return $this->callback ?: 'callback';

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

        return json_decode($this->params['authToken'], true);

    }
}