<?php


namespace Core\Module;


use Core\Expression\AbstractKeyPath;
use Core\Validation\ConstraintsProvider;

class ModelAspect {

    private $model;
    private $prefix;
    private $constraints;
    private $keyPath;

    function __construct($model, $prefix, $constraints, $keyPath) {
        $this->model = $model;
        $this->prefix = $prefix;
        $this->constraints = $constraints;
        $this->keyPath = $keyPath;
    }
    /**
     * @return string
     */
    public function getModel () {
        return $this->model;
    }

    /**
     * @return string|null
     */
    public function getPrefix () {
        return $this->prefix;
    }

    /**
     * @return ConstraintsProvider[]
     */
    public function getConstraints () {
        return $this->constraints;
    }

    /**
     * @return AbstractKeyPath|null
     */
    public function getKeyPath () {
        return $this->keyPath;
    }

} 