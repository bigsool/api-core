<?php

namespace Core\Filter;

use Core\Expression\ExpressionWithOperator as ExpressionWithOperator;

class ExpressionFilter extends Filter {

    /**
     * @param string                 $entity
     * @param string                 $name
     * @param ExpressionWithOperator $expression
     */
    function __construct ($entity, $name, ExpressionWithOperator $expression) {

        parent::__construct($entity, $name, $expression);

    }

}
