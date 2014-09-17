<?php

namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Registry;

interface Expression {

    /**
     * @param Registry $registry
     * @param Context  $context
     *
     * @return string
     */
    public function resolve (Registry $registry, Context $context);

} 