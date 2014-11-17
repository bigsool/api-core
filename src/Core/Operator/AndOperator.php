<?php


namespace Core\Operator;


class AndOperator implements LogicOperator {

    /**
     * @return string
     */
    public function toDQL () {

        return 'AND';

    }
}