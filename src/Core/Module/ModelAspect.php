<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Field\RelativeField;
use Core\Validation\Parameter\Constraint;

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
     * @var Constraint[][]
     */
    private $constraints;

    /**
     * @var string|null
     */
    private $relativeField;

    /**
     * @var Action[]
     */
    private $actions;

    /**
     * @var boolean
     */
    private $withPrefixedFields;

    /**
     * @var ModuleEntity
     *
     */
    private $moduleEntity;


    /**
     * @param string                          $model
     * @param                                 $module
     * @param string                          $prefix
     * @param Constraint[][]                  $constraints
     * @param Action[]                        $actions
     * @param RelativeField|null              $relativeField
     * @param Boolean                         $withPrefixedFields
     */
    public function __construct ($model, $module, $prefix, array $constraints, array $actions,
                                 $relativeField = NULL, $withPrefixedFields = false) {

        $this->model = $model;
        $this->prefix = $prefix;
        $this->constraints = $constraints;
        $this->relativeField = $relativeField;
        $this->actions = $actions;
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
     * @return Constraint[]|Constraint[][]
     */
    public function getConstraints ($actionName = NULL) {

        return isset($actionName)
            ? (isset($this->constraints[$actionName])
                ? $this->constraints[$actionName]
                : [])
            : $this->constraints;

    }

    /**
     * @return string|null
     */
    public function getRelativeField () {

        return $this->relativeField;

    }

    /**
     * @param $action
     *
     * @return bool
     */

    public function isDisabledForAction($action) {
        return false;
    }



    /**
     * @return boolean
     */
    public function isWithPrefixedFields () {

        return $this->withPrefixedFields;

    }

    /**
     * @return bool
     */
    public function isMainAspect() {
        return $this->getRelativeField() == null;
    }

    /**
     * @return ModuleEntity
     */
    public function getModuleEntity () {

        return $this->moduleEntity;
    }

    /**
     * @param ModuleEntity $moduleEntity
     */
    public function setModuleEntity ($moduleEntity) {

        $this->moduleEntity = $moduleEntity;
    }

}