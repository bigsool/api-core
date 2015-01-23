<?php


namespace Core\Module;


use Core\Action\Action;
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
     * @var ConstraintsProvider[][]
     */
    private $constraints;

    /**
     * @var AbstractKeyPath|null
     */
    private $keyPath;

    /**
     * @var Action[]
     */
    private $actions;

    /**
     * @param string                  $model
     * @param string                  $prefix
     * @param ConstraintsProvider[][] $constraints
     * @param Action[]                $actions
     * @param AbstractKeyPath         $keyPath
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
     * @param null $actionName
     *
     * @return ConstraintsProvider[]|ConstraintsProvider[][]
     */
    public function getConstraints ($actionName = NULL) {

        return isset($actionName)
            ? (isset($this->constraints[$actionName])
                ? $this->constraints[$actionName]
                : [])
            : $this->constraints;

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

    /**
     * @param $actionName
     *
     * @return Action|null
     */
    public function getAction ($actionName) {

        return isset($this->actions[$actionName]) ? $this->actions[$actionName] : NULL;
    }

} 