<?php

namespace Core\Frontend;

use Core\Field\KeyPath;

abstract class Model {

    /**
     * @return string
     */
    public abstract function getMainEntityName();

    /**
     * Return all KeyPaths of this model
     * Should be used to fetch this model
     * @return KeyPath[]
     */
    public abstract function getAllKeyPaths();

    /**
     * Return Doctrine entities associated to this frontend model
     * Should be used to save this model
     * @return mixed
     */
    public abstract function getEntities();

} 