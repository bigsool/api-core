<?php


namespace Archiweb\Rule;


use Archiweb\Context\QueryContext;

abstract class Rule {

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $command
     * @param string $entity
     * @param string $name
     */
    public function __construct ($command, $entity, $name) {

        $this->command = $command;
        $this->entity = $entity;
        $this->name = $name;

    }

    /**
     * @param QueryContext $ctx
     *
     * @return bool
     */
    public function shouldApply (QueryContext $ctx) {

        /*
        $isThisInRules = function (array $rules) use (&$isThisInRules) {

            foreach ($rules as $rule) {
                if ($this === $rule || !$isThisInRules($rule->listChildRules())) {
                    return false;
                }
            }

            return true;
        };

        return $isThisInRules($ctx->getRules());
        */

    }

    /**
     * @return Rule[]
     */
    public abstract function listChildRules ();

    /**
     * @param QueryContext $ctx
     */
    public abstract function apply (QueryContext $ctx);

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }

    /**
     * @return string
     */
    public function getCommand () {

        return $this->command;

    }

} 