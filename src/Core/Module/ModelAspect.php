<?php


namespace Core\Module;


use Core\Expression\AbstractKeyPath;
use Core\Validation\ConstraintsProvider;

class ModelAspect {

    /**
     * @var string
     */
    private $model;

    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @var ConstraintsProvider[]|null
     */
    private $constraints;

    /**
     * @var AbstractKeyPath|null
     */
    private $keyPath;

    /**
     * @var Action[]|null
     */
    private $actions;

    /**
     * @param string                $model
     * @param string                $prefix
     * @param ConstraintsProvider[] $constraints
     * @param Action[]              $actions
     * @param AbstractKeyPath       $keyPath
     */
    public function __construct ($model, $prefix, array $constraints, array $actions, $keyPath) {

        $this->model = $model;
        $this->prefix = $prefix;
        $this->constraints = $constraints;
        $this->keyPath = $keyPath;
        $this->actions = $actions;
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
     * @return ConstraintsProvider[]|null
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

    /**
     * @return Action[]|null
     */
    public function getActions () {

        return $this->actions;
    }

} 