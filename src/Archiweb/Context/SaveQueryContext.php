<?php


namespace Archiweb\Context;


class SaveQueryContext implements QueryContext {

    /**
     * @param ApplicationContext $ctx
     * @param                    $model
     */
    public function __construct (ApplicationContext $ctx, $model) {

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {
        // TODO: Implement getApplicationContext() method.
    }

    /**
     * @return string
     */
    public function getEntity () {
        // TODO: Implement getEntity() method.
    }
}