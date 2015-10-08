<?php


namespace Core\RPC;


use Core\Error\FormattedError;
use Core\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CLI extends Custom {

    /**
     * @param Request $request
     *
     * @throws \Core\Error\FormattedError
     */
    public function parse (Request $request) {

        global $argv;

        $this->setModule($argv[1])
             ->setAction($argv[2])
             ->setParams([])
             ->setReturnedFields([])
             ->setIpAddress('localhost');

    }

}