<?php


namespace Core\Helper;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;

class BasicHelper {

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    /**
     * @param ApplicationContext $appCtx
     */
    public function __construct(ApplicationContext $appCtx) {

        $this->appCtx = $appCtx;

    }

    /**
     * @param string $modelName
     *
     * @return mixed
     */
    public function createRealModel ($modelName) {

        $className = $this->getRealModelClassName($modelName);

        return new $className;

    }

    /**
     * @param string $modelName
     *
     * @return string
     */
    public function getRealModelClassName ($modelName) {

        return $this->appCtx->getRealModelClassName($modelName);

    }

    /**
     * @param mixed  $model
     * @param string $modelName
     */
    public function checkRealModelType ($model, $modelName) {

        $className = $this->getRealModelClassName($modelName);
        if (!is_a($model, $className)) {
            throw new \RuntimeException("Excepted type is $className");
        }

    }

    /**
     * @param object $model
     * @param array  $params
     *
     * @return object
     */
    public function basicSetValues ($model, array $params) {

        if (!is_object($model)) {
            throw new \RuntimeException('$model must be an object');
        }

        foreach ($params as $field => $param) {
            $method = 'set' . ucfirst($field);
            if (!is_callable([$model, $method], false, $callableName)) {
                throw new \RuntimeException($callableName . ' is not callable');
            }
            $model->$method($param);
        }

        return $model;

    }

    /**
     * @param FindQueryContext $context
     * @param RelativeField[]  $keyPaths
     * @param Filter[]         $filters
     * @param array            $params
     *
     * @return \mixed[]
     */
    public function basicFind (FindQueryContext $context, array $keyPaths = [], array $filters = [], array $params = []) {

        foreach ($keyPaths as $keyPath) {
            $context->addField($keyPath);
        }

        foreach ($filters as $filter) {
            $context->addFilter($filter);
        }

        $context->setParams($params);

        return $context->findAll();

    }

} 