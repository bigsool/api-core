<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Field;

class FieldRule implements Rule {

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @param Field $field
     * @param Rule  $rule
     */
    public function __construct (Field $field, Rule $rule) {

        $entity = $field->getEntity();

        if ($entity != $rule->getEntity()) {
            throw new \RuntimeException('incompatible rule');
        }

        $this->field = $field;
        $this->rule = $rule;

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {

        return [$this->getRule()];

    }

    /**
     * @return Rule
     */
    public function getRule () {

        return $this->rule;

    }

    /**
     * @param FindQueryContext $ctx
     */
    public function apply (FindQueryContext $ctx) {

        $this->getRule()->apply($ctx);

    }

    /**
     * @param QueryContext $ctx
     *
     * @return bool
     */
    public function shouldApply (QueryContext $ctx) {
        // TODO: Implement shouldApply() method.
    }

    /**
     * @return string
     */
    public function getName () {

        return $this->getField()->getEntity() . ucfirst($this->getField()->getName()) . 'FieldRule';

    }

    /**
     * @return Field
     */
    public function getField () {

        return $this->field;

    }
}