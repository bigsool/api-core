<?php

namespace Archiweb\Expression;


interface ExpressionWithOperator extends Expression
{

    /**
     * @return Archiweb\Operator
     */
    public function getOperator();

} 