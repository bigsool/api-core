<?php

namespace Core\Validation;


use Core\Validation\Parameter\Constraint;

class ParameterFactory {

    /**
     * @var Constraint[]
     */
    protected $parameters = [];

    /**
     * @param string $class
     * @param mixed  $options
     *
     * @return Constraint
     */
    public function getParameter ($class, $options = NULL) {

        if ($options !== NULL) {
            return new $class($options);
        }
        if (!array_key_exists($class, $this->parameters)) {
            $this->parameters[$class] = new $class;
        }

        return $this->parameters[$class];
    }

}

