<?php


namespace Archiweb\Rule;


use Archiweb\ActionContext;

class SimpleRule implements Rule {

    /**
     * @param string $command
     * @param string $entity
     * @param string $name
     * @param Filter $filter
     */
    public function __construct ($command, $entity, $name, Filter $filter) {
        // TODO: Implement constructor
    }

    /**
     * @param ActionContext $ctx
     *
     * @return bool
     */
    public function shouldApply (ActionContext $ctx) {
        // TODO: Implement shouldApply() method.
    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {
        // TODO: Implement listChildRules() method.
    }

    /**
     * @param ActionContext $ctx
     */
    public function apply (ActionContext $ctx) {
        // TODO: Implement apply() method.
    }

    /**
     * @return string
     */
    public function getName () {
        // TODO: Implement getName() method.
    }

    /**
     * @return string
     */
    public function getEntity () {
        // TODO: Implement getEntity() method.
    }
}