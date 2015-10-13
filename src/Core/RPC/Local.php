<?php


namespace Core\RPC;


use Core\Action\Action;

abstract class Local extends Handler {

    /**
     * @return string|Action
     */
    public abstract function getAction ();

    /**
     * @return string
     */
    public abstract function getModule ();

}