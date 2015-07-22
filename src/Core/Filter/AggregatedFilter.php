<?php

namespace Core\Filter;

use Core\Expression\NAryExpression as NAryExpression;
use Core\Operator\LogicOperator as LogicOperator;

class AggregatedFilter extends Filter {

    /**
     * @var LogicOperator
     */
    private $operator;

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * @param string        $entity
     * @param string        $name
     * @param LogicOperator $operator
     * @param Filter[]         $filters
     */
    function __construct ($entity, $name, LogicOperator $operator, array $filters) {

        parent::__construct($entity, $name, NULL);
        $this->operator = $operator;

        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

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
