<?php


namespace Core\Module;


use Core\Context\ApplicationContext;

class BasicHelper {

    /**
     * @param object $model
     * @param array  $params
     * @param bool   $shouldSave
     *
     * @return object
     */
    public function basicSave ($model, array $params, $shouldSave = true) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        foreach ($params as $field => $param) {
            $method = 'set' . ucfirst($field);
            $model->$method($param);
        }

        if ($shouldSave) {
            $registry->save($model);
        }

        return $model;

    }

} 