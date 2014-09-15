<?php

namespace Archiweb\Filter;
	
class CallbackFilter extends Filter {

    private $command;
    private $callback;

    function __construct ($entity, $name, $command, $callback) {

        parent::__construct($entity,$name,null);
        $this->command = $command;
        $this->callback = $callback;

    }

    public function getExpression () {

        return call_user_func($this->callback);

    }

}
