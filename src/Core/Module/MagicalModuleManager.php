<?php


namespace Core\Module;


use Core\Parameter\Parameter;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

    }

    /**
     * @param Parameter[] $params
     */
    protected function magicalCreate (array $params) {

    }

    /**
     * @param string   $name
     * @param callable $processFn
     */
    protected function defineAction ($name, callable $processFn) {

    }

} 