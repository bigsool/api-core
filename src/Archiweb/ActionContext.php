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
     * @param Context $context
     */
    public function __construct (Context $context) {

        $this->setParams($context->getParams());

    }

    /**.
     * @return Filter[]
     */
    public function getFilters () {
        // TODO: Implement getFilters() method
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

}