<?php


namespace Core\Module;


use Core\Context\ApplicationContext;

class BasicHelper {

    /**
     * @param $model
     *
     * @return string
     */
    public function realModelClassName ($model) {

        return ApplicationContext::getInstance()->getNewRegistry()->realModelClassName($model);

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