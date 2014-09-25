<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Filter\Filter;

class SimpleRule extends Rule {

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param string $command
     * @param string $entity
     * @param string $name
     * @param Filter $filter
     */
    public function __construct ($command, $entity, $name, Filter $filter) {

        parent::__construct($command, $entity, $name);
        $this->filter = $filter;

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {

        return [];

    }

    /**
     * @param FindQueryContext $ctx
     */
    public function apply (FindQueryContext $ctx) {

        $ctx->addFilter($this->getFilter());

    }

    /**
     * @return Filter
     */
    public function getFilter () {

        return $this->filter;

    }

}