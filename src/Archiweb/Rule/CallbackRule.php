<?php


namespace Archiweb\Rule;


use Archiweb\ActionContext;

class CallbackRule extends Rule {

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var Rule[]|array
     */
    protected $childRules;

    /**
     * @param string   $command
     * @param string   $entity
     * @param string   $name
     * @param callable $callback
     * @param Rule[]   $childRuleList
     */
    public function __construct ($command, $entity, $name, callable $callback, array $childRuleList) {

        parent::__construct($command, $entity, $name);
        $this->callback = $callback;
        $this->childRules = $childRuleList;

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {

        return $this->childRules;

    }

    /**
     * @param ActionContext $ctx
     */
    public function apply (ActionContext $ctx) {

        call_user_func($this->getCallback(), $ctx);

    }

    /**
     * @return callable
     */
    public function getCallback () {

        return $this->callback;

    }
}