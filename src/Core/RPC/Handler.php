<?php


namespace Core\RPC;


use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Handler {

    /**
     * @param Request $request
     */
    public function parse (Request $request);

    /**
     * @return Response
     */
    public function getErrorResponse ();

    /**
     * @param Serializer $serializer
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer);

    /**
     * @param mixed $data
     */
    public function setResult($data);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param FormattedError $error
     */
    public function setError($error);

    /**
     * @return FormattedError
     */
    public function getError();

    /**
     * @return string
     */
    public function getPath ();

    /**
     * @return string
     */
    public function getService ();

    /**
     * @return string
     */
    public function getMethod ();

    /**
     * @return string
     */
    public function getClientName ();

    /**
     * @return string
     */
    public function getClientVersion ();

    /**
     * @return string
     */
    public function getLocale ();

    /**
     * @return array
     */
    public function getParams ();

    /**
     * @return string[]
     */
    public function getReturnedFields ();

    /**
     * @return string
     */
    public function getIpAddress ();

    /**
     * @return array
     */
    public function getAuthToken ();

} 