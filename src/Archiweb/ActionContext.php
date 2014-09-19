<?php


namespace Archiweb;


use Archiweb\Filter\Filter;
use Archiweb\Rule\Rule;

class ActionContext extends Context {

    /**
     * @var Rule[]
     */
    protected $rules = array();

    /**
     * @var Filter[]
     */
    protected $filters = array();

    /**
     * @param Context $context
     */
    public function __construct (Context $context) {

        $this->setParams($context->getParams());
        $this->setEntityManager($context->getEntityManager());

    }

    /**.
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @return Rule[]
     */
    public function getRules () {

        return $this->rules;

    }

    /**
     * @param Rule $rule
     */
    public function addRule (Rule $rule) {

        if (!in_array($rule, $this->rules, true)) {
            $this->rules[] = $rule;
        }

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        if (!in_array($filter, $this->filters, true)) {
            $this->filters[] = $filter;
        }

    }

}