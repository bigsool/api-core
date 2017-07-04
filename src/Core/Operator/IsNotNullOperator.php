<?php


namespace Core\Operator;


class IsNotNullOperator implements CompareOperator {

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function toDQL ($value = NULL) {

        return 'IS NOT NULL';

    }
}