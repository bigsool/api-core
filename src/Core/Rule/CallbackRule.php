<?php


namespace Core\Rule;


use Core\Context\QueryContext;


class CallbackRule implements Rule {

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var Rule[]|array
     */
    protected $childRules;

    /**
     * @var callable
     */
    protected $shouldApplyCb;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param          $name
     * @param callable $shouldApplyCb
     * @param callable $callback
     * @param Rule[]   $childRuleList
     */
    public function __construct ($name, callable $shouldApplyCb, callable $callback, array $childRuleList) {

        foreach ($childRuleList as $childRule) {
            if (!($childRule instanceof Rule)) {
                throw new \RuntimeException('invalid child rule type');
            }
        }

        $this->name = $name;
        $this->callback = $callback;
        $this->childRules = $childRuleList;
        $this->shouldApplyCb = $shouldApplyCb;

    }

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {

        call_user_func($this->getCallback(), $ctx);

    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {

        return $this->childRules;

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
     * @return callable
     */
    public function getCallback () {

        return $this->callback;

    }
}