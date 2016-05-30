<?php

namespace Core\Helper;


class ClassHelper {

    protected static $alreadySearchedClasses = [];

    public static function classExists ($class, $autoload = true, $force = false) {

        if (!$force && !array_key_exists($class, static::$alreadySearchedClasses)) {
            static::$alreadySearchedClasses[$class] = class_exists($class, $autoload);
        }

        return static::$alreadySearchedClasses[$class] = class_exists($class, $autoload);

    }

}