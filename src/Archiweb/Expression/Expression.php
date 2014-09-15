<?php

namespace Archiweb\Expression;


interface Expression
{

    /**
     * @param Archiweb\Registry $registry
     * @param Archiweb\Context $context
     * @return string
     */
    public function resolve($registry, $context);

} 