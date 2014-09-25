<?php


namespace Archiweb\Rule;


use Archiweb\Context\QueryContext;
use Archiweb\Field;

class FieldRule extends Rule {

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
        $fieldName = $field->getName();
        $name = $entity . ucfirst($fieldName) . 'FieldRule';

        if ($entity != $rule->getEntity()) {
            throw new \RuntimeException('incompatible rule');
        }

        parent::__construct($rule->getCommand(), $entity, $name);

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
     * @return Field
     */
    public function getRule () {

        return $this->rule;

    }

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {
        // TODO: Implement apply() method.
    }

    /**
     * @return Field
     */
    public function getField () {

        return $this->field;

    }
}