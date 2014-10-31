<?php

namespace Archiweb\Filter;

class CallbackFilter extends Filter {

    private $command;

    private $callback;

    /**
     * @param string   $entity
     * @param string   $name
     * @param callable $callback
     */
    function __construct ($entity, $name, callable $callback) {

        parent::__construct($entity, $name, NULL);
        $this->callback = $callback;

    }

    /**
     * @return Expression
     */
    public function getExpression () {

        return call_user_func($this->callback);

    }

}
