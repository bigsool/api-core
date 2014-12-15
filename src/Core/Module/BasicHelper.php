<?php


namespace Core\Module;


use Core\Context\ApplicationContext;
use Core\Parameter\Parameter;

class BasicHelper {

    /**
     * @param string      $entity
     * @param Parameter[] $params
     * @param bool        $shouldSave
     */
    public function create ($entity, array $params, $shouldSave = true) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $class = $registry::realModelClassName($entity);
        $model = new $class;

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