<?php

namespace Archiweb\Filter;

use Archiweb\Expression\NAryExpression as NAryExpression;
use Archiweb\Operator\LogicOperator as LogicOperator;

class AggregatedFilter extends Filter {

    private $command;

    private $operator;

    private $filters;

    /**
     * @param string        $entity
     * @param string        $name
     * @param string        $command
     * @param LogicOperator $operator
     */
    function __construct ($entity, $name, $command, LogicOperator $operator) {

        parent::__construct($entity, $name, NULL);
        $this->command = $command;
        $this->operator = $operator;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @return Filter
     */
    public function getFilters () {

        return $this->filters ? $this->filters : NULL;

    }

    /**
     * @return NAryExpression
     */
    public function getExpression () {

        if (!$this->filters) {
            return NULL;
        }

        foreach ($this->filters as $filter) {
            $expressions[] = $filter->getExpression();
        }

        return new NAryExpression($this->operator, $expressions);

    }

}
