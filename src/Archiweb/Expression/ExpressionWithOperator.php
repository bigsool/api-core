<?php

namespace Archiweb\Expression;


use Archiweb\Operator\Operator;

interface ExpressionWithOperator extends Expression {

    /**
     * @return Operator
     */
    public function getOperator ();

} 