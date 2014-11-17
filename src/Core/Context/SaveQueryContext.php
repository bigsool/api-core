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

        $class = new \ReflectionClass($model);
        if ($class->getNamespaceName() != 'Core\Model') {
            throw new \RuntimeException('invalid model class');
        }

        $this->entity = $class->getShortName();
        $this->model = $model;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }
}