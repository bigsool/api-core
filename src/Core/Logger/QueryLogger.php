<?php

namespace Core\Logger;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QueryLogger extends AbstractLogger {

    /**
     * @param Request $request
     */
    public function logRequest (Request $request) {

        $ip = $request->getClientIp() ?: '-';
        $method = $request->getMethod();
        $uri = $request->getUri();
        $get = json_encode($request->query->all());
        $post = json_encode($request->request);
        $server = json_encode($request->server->all());

        $this->getMLogger()->addInfo("<-- {$ip} {$method} {$uri} {$get} {$post} {$server}");

    }

    /**
     * @param Response $response
     */
    public function logResponse (Response $response) {

        $headers = json_encode($response->headers->all());
        $body = $response->getContent();

        $this->getMLogger()->addInfo("--> {$headers} {$body}");

    }

    /**
     * @return string
     */
    public function getChannel () {

        return 'queries';

    }
}