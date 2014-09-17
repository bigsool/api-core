<?php


namespace Archiweb\Operator;


class InOperator implements CompareOperator {

    /**
     * @param null $value
     *
     * @return string
     * @throws \RuntimeException
     */
    public function toDQL ($value = NULL) {

        if (!is_string($value) && !is_null($value)) {
            throw new \RuntimeException('invalid format');
        }

        return "IN ($value)";
    }

}