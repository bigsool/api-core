<?php


namespace Archiweb\Context;


class SaveQueryContext implements QueryContext {

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @param ApplicationContext $ctx
     * @param                    $model
     */
    public function __construct (ApplicationContext $ctx, $model) {

        if (!is_object($model)) {
            throw new \RuntimeException('invalid model type');
        }

        $class = new \ReflectionClass($model);
        if ($class->getNamespaceName() != 'Archiweb\Model') {
            throw new \RuntimeException('invalid model class');
        }

        $this->entity = $class->getShortName();
        $this->appCtx = $ctx;
        $this->model = $model;

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->appCtx;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }
}