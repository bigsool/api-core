<?php

namespace Core\Filter;

use Core\Expression\Expression;

class CallbackFilter extends Filter {

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
