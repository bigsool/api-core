<?php

namespace Core\Module;


abstract class MagicalEntity {

    /**
     * @return mixed
     */
    public abstract function getMainEntity ();

    /**
     * @return mixed
     */
    public abstract function getId ();

}