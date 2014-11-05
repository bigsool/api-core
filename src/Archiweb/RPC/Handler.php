<?php


namespace Archiweb\RPC;


use Archiweb\Error\FormattedError;
use Archiweb\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Handler {

    /**
     * @param Request $request
     */
    public function parse (Request $request);

    /**
     * @param FormattedError $error
     *
     * @return Response
     */
    public function getErrorResponse (FormattedError $error);

    /**
     * @param Serializer $serializer
     * @param mixed      $data
     *
     * @return Response
     */
    public function getSuccessResponse (Serializer $serializer, $data);

    /**
     * @return string
     */
    public function getPath ();

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
     * @return string
     */
    public function getReturnedRootEntity ();

    /**
     * @return string[]
     */
    public function getReturnedFields ();

} 