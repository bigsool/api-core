<?php


namespace Core\Module;


use Core\Expression\AbstractKeyPath;
use Core\Validation\ConstraintsProvider;

interface ModelAspect {

    /**
     * @return string
     */
    public function getModel ();

    /**
     * @return string|null
     */
    public function getPrefix ();

    /**
     * @return ConstraintsProvider[]
     */
    public function getValidators ();

    /**
     * @return AbstractKeyPath|null
     */
    public function getKeyPath ();

} 