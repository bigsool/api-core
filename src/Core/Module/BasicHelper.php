<?php


namespace Core\Module;


use Core\Context\ApplicationContext;

class BasicHelper {

    /**
     * @param $modelName
     *
     * @return string
     */
    public function getRealModelClassName ($modelName) {

        return ApplicationContext::getInstance()->getNewRegistry()->realModelClassName($modelName);

    }

    /**
     * @param string $modelName
     *
     * @return mixed
     */
    public function createRealModel($modelName) {

        $className = $this->getRealModelClassName($modelName);
        return new $className;

    }

    /**
     * @param mixed $model
     * @param string $modelName
     */
    public function checkRealModelType($model, $modelName) {

        $className = $this->getRealModelClassName($modelName);
        if (!is_a($model, $className)) {
            throw new \RuntimeException("Excepted type is $className");
        }

    }

    /**
     * @param object $model
     * @param array  $params
     * @param bool   $shouldSave
     *
     * @return object
     */
    public function basicSave ($model, array $params, $shouldSave = true) {

        if (!is_object($model)) {
            throw new \RuntimeException('$model must be an object');
        }

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        foreach ($params as $field => $param) {
            $method = 'set' . ucfirst($field);
            if (!is_callable([$model, $method], false, $callableName)) {
                throw new \RuntimeException($callableName . ' is not callable');
            }
            $model->$method($param);
        }

        if ($shouldSave) {
            $registry->save($model);
        }

        return $model;

    }

} 