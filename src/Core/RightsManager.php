<?php


namespace Core;


abstract class RightsManager {

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @param Auth $auth
     */
    public function __construct (Auth $auth) {

        $this->auth = $auth;

    }

    /**
     * @return Auth
     */
    public function getAuth () {

        return $this->auth;

    }

    /**
     * @param $right
     *
     * @return bool
     */
    abstract public function hasRight ($right);

}