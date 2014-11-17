<?php

namespace Core\Expression;


use Core\Operator\Operator;

interface ExpressionWithOperator extends Expression {

    /**
     * @return Operator
     */
    public function getOperator ();

} 