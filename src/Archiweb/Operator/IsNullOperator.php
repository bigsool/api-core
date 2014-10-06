<?php


namespace Archiweb\Operator;


class IsNullOperator implements CompareOperator {

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function toDQL ($value = NULL) {

        return 'IS NULL';

    }
}