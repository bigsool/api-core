<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Expression\AbstractKeyPath;
use Core\Validation\AbstractConstraintsProvider;

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
     * @var AbstractConstraintsProvider[][]
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
     * @var boolean
     */
    private $enabled;

    /**
     * @param string                          $model
     * @param string                          $prefix
     * @param AbstractConstraintsProvider[][] $constraints
     * @param Action[]                        $actions
     * @param AbstractKeyPath                 $keyPath
     */
    public function __construct ($model, $prefix, array $constraints, array $actions, $keyPath) {

        $this->model = $model;
        $this->prefix = $prefix;
        $this->constraints = $constraints;
        $this->keyPath = $keyPath;
        $this->actions = $actions;
        $this->enabled = true;

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
     * @return AbstractConstraintsProvider[]|AbstractConstraintsProvider[][]
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

    public function enable () {

        $this->enabled = true;

    }

    public function disable () {

        $this->enabled = false;

    }

    /**
     * @return boolean
     */
    public function isEnabled () {

        return $this->enabled;

    }

}