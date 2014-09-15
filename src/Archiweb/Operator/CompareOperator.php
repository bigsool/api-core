<?php


namespace Archiweb\Operator;


interface CompareOperator extends Operator
{

    /**
     * @param string $value
     * @return string
     */
    public function toDQL($value);

} 