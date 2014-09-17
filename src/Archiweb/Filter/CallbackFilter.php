<?php

namespace Archiweb\Filter;

class CallbackFilter extends Filter {

    private $command;

    private $callback;

    /**
     * @param string   $entity
     * @param string   $name
     * @param string   $command
     * @param Function $callback
     */
    function __construct ($entity, $name, $command, $callback) {

        parent::__construct($entity, $name, NULL);
        $this->command = $command;
        $this->callback = $callback;

    }

    /**
     * @return Expression
     */
    public function getExpression () {

        return call_user_func($this->callback);

    }

}
