<?php

namespace Archiweb\Expression;


use Archiweb\Context\QueryContext;
use Archiweb\Registry;

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
    public function getExpressions();

} 