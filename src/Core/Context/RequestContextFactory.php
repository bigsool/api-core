<?php

namespace Core\Context;

class RequestContextFactory {

    /**
     * @return RequestContext
     */
    public function getNewRequestContext () {

        return RequestContext::_init();

    }

}