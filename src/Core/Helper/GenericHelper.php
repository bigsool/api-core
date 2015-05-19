<?php


namespace Core\Helper;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class GenericHelper {

    /**
     * @var BasicHelper
     */
    protected $helper;

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @param ApplicationContext $applicationContext
     * @param string             $modelName
     */
    public function __construct (ApplicationContext $applicationContext, $modelName) {

        $this->helper = new BasicHelper($applicationContext);
        $this->modelName = $modelName;

    }

    /**
     * @param ActionContext $actionContext
     * @param array         $values
     *
     * @return mixed
     */
    public function create (ActionContext $actionContext, array $values) {

        $entity = $this->helper->createRealModel($this->modelName);

        $this->helper->basicSetValues($entity, $values);

        return $actionContext[$this->modelName] = $entity;

    }

    /**
     * @param ActionContext $actionContext
     * @param mixed         $model
     * @param array         $values
     *
     * @return mixed
     */
    public function update (ActionContext $actionContext, $model, array $values) {

        $this->helper->checkRealModelType($model, $this->modelName);

        $this->helper->basicSetValues($model, $values);

        return $actionContext[$this->modelName] = $model;

    }

}