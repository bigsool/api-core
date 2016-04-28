<?php


namespace Core\RPC;


use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Handler {

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var FormattedError
     */
    protected $error;

    /**
     * @param Request $request
     */
    public abstract function parse (Request $request);

    /**
     * @return Response
     */
    public abstract function getErrorResponse ();

    /**
     * @param Serializer $serializer
     *
     * @return Response
     */
    public abstract function getSuccessResponse (Serializer $serializer);

    /**
     * @param mixed $data
     */
    public function setResult ($data) {

        $this->result = $data;

    }

    /**
     * @return mixed
     */
    public function getResult () {

        return $this->result;

    }

    /**
     * @param FormattedError $error
     */
    public function setError (FormattedError $error) {

        $this->error = $error;

    }

    /**
     * @return FormattedError
     */
    public function getError () {

        return $this->error;

    }

    /**
     * @return string
     */
    public abstract function getPath ();

    /**
     * @return string
     */
    public abstract function getService ();

    /**
     * @return string
     */
    public abstract function getMethod ();

    /**
     * @return string
     */
    public abstract function getClientName ();

    /**
     * @return string
     */
    public abstract function getClientVersion ();

    /**
     * @return string
     */
    public abstract function getLocale ();

    /**
     * @return array
     */
    public abstract function getParams ();

    /**
     * @return string[]
     */
    public abstract function getReturnedFields ();

    /**
     * @return string
     */
    public abstract function getIpAddress ();

    /**
     * @return array
     */
    public abstract function getAuthToken ();

} 