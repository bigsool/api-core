<?php


namespace Core\Operator;


interface CompareOperator extends Operator {

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function toDQL ($value = NULL);

} 