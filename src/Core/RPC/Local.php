<?php


namespace Core\RPC;


use Core\Action\Action;

interface Local extends Handler {

    /**
     * @return string|Action
     */
    public function getAction ();

    /**
     * @return string
     */
    public function getModule ();

}