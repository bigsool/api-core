<?php

namespace Core\Module;


abstract class MagicalEntity {

    public abstract function getMainEntity ();

    /**
     * @return MagicalModuleManager
     */
    public abstract function getMagicalModuleManager ();

}