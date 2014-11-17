<?php


namespace Core\Parameter;


class SafeParameter extends Parameter {

    /**
     * @return bool
     */
    public function isSafe () {

        return true;

    }
}