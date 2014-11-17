<?php


namespace Core\Parameter;


class UnsafeParameter extends Parameter {

    /**
     * @return bool
     */
    public function isSafe () {

        return false;

    }
}