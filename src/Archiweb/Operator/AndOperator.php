<?php


namespace Archiweb\Operator;


class AndOperator implements LogicOperator
{

    /**
     * @return string
     */
    public function toDQL()
    {
        return 'AND';
    }
}