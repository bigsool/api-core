<?php 
	
namespace Archiweb\Filter;

use Archiweb\Operator\LogicOperator as LogicOperator;

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

}
