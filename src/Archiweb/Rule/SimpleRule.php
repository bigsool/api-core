<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Filter\Filter;

class SimpleRule implements Rule {

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $shouldApplyCb;

    /**
     * @param string   $name
     * @param callable $shouldApplyCb
     * @param Filter   $filter
     */
    public function __construct ($name, callable $shouldApplyCb, Filter $filter) {

        $this->filter = $filter;
        $this->name = name;
        $this->shouldApplyCb = $shouldApplyCb;

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

    /**
     * @param QueryContext $ctx
     *
     * @return bool
     */
    public function shouldApply (QueryContext $ctx) {

        return call_user_func($this->shouldApplyCb, $ctx);

    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }
}