<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Field\RelativeField;
use Core\Validation\AbstractConstraintsProvider;

class ModelAspect {

    /**
     * @var string
     */
    protected $module;

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
     * @var RelativeField|null
     */
    private $relativeField;

    /**
     * @var Action[]
     */
    private $actions;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var boolean
     */
    private $withPrefixedFields;

    /**
     * @param string                          $model
     * @param                                 $module
     * @param string                          $prefix
     * @param AbstractConstraintsProvider[][] $constraints
     * @param Action[]                        $actions
     * @param RelativeField|null              $relativeField
     * @param Boolean                         $withPrefixedFields
     */
    public function __construct ($model, $module, $prefix, array $constraints, array $actions,
                                 RelativeField $relativeField = NULL, $withPrefixedFields = false) {

        $this->model = $model;
        $this->prefix = $prefix;
        $this->constraints = $constraints;
        $this->relativeField = $relativeField;
        $this->actions = $actions;
        $this->enabled = true;
        $this->withPrefixedFields = $withPrefixedFields;

        $this->module = $module;

    }

    /**
     * @return string
     */
    public function getModule () {

        return $this->module;

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
     * @return RelativeField|null
     */
    public function getRelativeField () {

        return $this->relativeField;

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

    /**
     * @return boolean
     */
    public function isWithPrefixedFields () {

        return $this->withPrefixedFields;

    }

}