<?php

namespace Core;

require_once($_SERVER['DOCUMENT_ROOT']."/archiweb/include/lib/dispatcher/localDispatcher.php");

function callV1API ($service, $method, $params) {

    try {
        $result = callLocalAPI($service, $method, $params)->getResult();
    }
    catch(\Exception $e) {
        throw $e;
    }

    return $result;

}