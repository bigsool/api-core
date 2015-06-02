<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Field\RelativeField;

class ModelAspect {

    /**
     * @var string
     */
    protected $module;

    /**
     * @var bool[]
     */
    protected $shouldBeIgnored;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string|null
     */
    private $prefix;

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
     * @param string                          $module
     * @param string                          $prefix
     * @param Action[]                        $actions
     * @param bool[]                          $shouldBeIgnored
     * @param RelativeField|null              $relativeField
     * @param Boolean                         $withPrefixedFields
     */
    public function __construct ($model, $module, $prefix, array $actions, array $shouldBeIgnored,
                                 $relativeField = NULL,
                                 $withPrefixedFields = false) {

        $this->model = $model;
        $this->prefix = $prefix;
        $this->relativeField = $relativeField;
        $this->actions = $actions;
        $this->withPrefixedFields = $withPrefixedFields;
        $this->shouldBeIgnored = $shouldBeIgnored;
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
     * @param string $action
     *
     * @return bool
     */

    public function isDisabledForAction ($action) {

        return array_key_exists($action, $this->shouldBeIgnored) ? $this->shouldBeIgnored[$action] : false;

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
    public function isMainAspect () {

        return $this->getRelativeField() == NULL;

    }

    /**
     * @return string|null
     */
    public function getRelativeField () {

        return $this->relativeField;

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