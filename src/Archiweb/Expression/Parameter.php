<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Registry;

class Parameter extends Value {

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!is_string($value)) {
            throw new \RuntimeException('invalid type');
        }
        if (!preg_match('/^:[a-zA-Z_0-9-]+$/', $value)) {
            throw new \RuntimeException('invalid format');
        }
        parent::__construct($value);
    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        if (!($context instanceof FindQueryContext)) {
            throw new \RuntimeException('invalid context');
        }

        $name = substr($this->getValue(), 1);
        $value = $context->getParam($name);

        if (is_null($value)) {
            throw new \RuntimeException("parameter $name not found");
        }

        $uniqueName = uniqid($this->getValue() . '-');

        $registry->setParameter($uniqueName, $value);

        return $uniqueName;

    }

} 