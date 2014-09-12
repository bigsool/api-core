<?php 

	class AggregatedFilter implements Filter {

		private $command;
		private $operator;

		function __construct ($entity, $command, $name, LogicOperator $operator) {}
		
		function addFilter ($filter) {}
		
	}

?>