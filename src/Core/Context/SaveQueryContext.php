<?php


namespace Core\Context;


class SaveQueryContext implements QueryContext {

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @param $model
     */
    public function __construct ($model) {

        if (!is_object($model)) {
            throw new \RuntimeException('invalid model type');
        }

        if (!ApplicationContext::getInstance()->isEntity($model)) {
            throw new \RuntimeException('invalid model class');
        }

        $this->entity = (new \ReflectionClass($model))->getShortName();
        $this->model = $model;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }
}