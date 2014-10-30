<?php


namespace Archiweb\RPC;


use Symfony\Component\HttpFoundation\Request;

interface Handler {

    /**
     * @param Request $request
     */
    public function __construct (Request $request);

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
    public function getReturnedRootEntity();

    /**
     * @return string[]
     */
    public function getReturnedFields();

} 