<?php


namespace Archiweb\RPC;


use Archiweb\Context\ApplicationContext;
use Symfony\Component\HttpFoundation\Request;

interface Handler {

    /**
     * @param ApplicationContext $context
     * @param Request            $request
     */
    public function __construct (ApplicationContext $context, Request $request);

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

} 