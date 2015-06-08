<?php


namespace Core\Helper;


interface ModuleManagerHelperLoader {

    /**
     * @param string $helperName
     *
     * @return string|false
     */
    public static function getHelperClassName($helperName);

}