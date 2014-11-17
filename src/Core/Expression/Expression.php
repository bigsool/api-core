<?php

namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Registry;

interface Expression {

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context);

    /**
     * @return Expression[]
     */
    public function getExpressions ();

} 