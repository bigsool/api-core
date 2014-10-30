<?php

namespace Archiweb\Filter;

use Archiweb\Expression\NAryExpression as NAryExpression;
use Archiweb\Operator\LogicOperator as LogicOperator;

class AggregatedFilter extends Filter {

    private $command;

    private $operator;

    private $filters = [];

    /**
     * @param string        $entity
     * @param string        $name
     * @param LogicOperator $operator
     */
    function __construct ($entity, $name, LogicOperator $operator) {

        parent::__construct($entity, $name, NULL);
        $this->operator = $operator;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @return NAryExpression
     */
    public function getExpression () {

        $expressions = [];

        if ($this->filters) {

            foreach ($this->filters as $filter) {
                $expressions[] = $filter->getExpression();
            }

        }

        return new NAryExpression($this->operator, $expressions);

    }

}
