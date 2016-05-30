<?php


namespace Core\RPC;


use Symfony\Component\HttpFoundation\Request;

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
             ->setParams($argv[3])
             ->setReturnedFields([])
             ->setIpAddress('localhost');

    }

}