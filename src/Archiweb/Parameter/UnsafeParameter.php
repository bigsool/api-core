<?php


namespace Archiweb\Parameter;


class UnsafeParameter extends Parameter {

    /**
     * @return bool
     */
    public function isSafe () {

        return false;

    }
}