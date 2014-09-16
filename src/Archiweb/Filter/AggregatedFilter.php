<?php 
	
namespace Archiweb\Filter;

use Archiweb\Expression\BinaryExpression;
use Archiweb\Operator\LogicOperator as LogicOperator;
use Archiweb\Expression\NAryExpression as NAryExpression;

class AggregatedFilter extends Filter {

    private $command;
    private $operator;
    private $filters;

    function __construct ($entity, $name, $command, LogicOperator $operator) {

        parent::__construct($entity,$name,null);
        $this->command = $command;
        $this->operator = $operator;

    }

    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    public function getFilters () {

        return $this->filters;

    }

    public function getExpression () {

        foreach ($this->filters as $filter) {
            $expressions[] = $filter->getExpression();
        }

        return new NAryExpression($this->$operator,$expressions);

    }

}
