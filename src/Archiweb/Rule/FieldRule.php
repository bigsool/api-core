<?php


namespace Archiweb\Rule;


use Archiweb\Context\QueryContext;
use Archiweb\Context\Rule\Rule;
use Archiweb\Field;
use Archiweb\Operation;

class FieldRule extends Rule {

    /**
     * @var Field
     */
    protected $field;

    /**
     * @param Field $field
     */
    public function __construct (Field $field) {

        $entity = $field->getEntity();
        $fieldName = $field->getName();
        $name = $entity . lcfirst($fieldName) . 'FieldRule';

        parent::__construct(Operation::SELECT, $entity, $name);

        $this->field = $field;

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {
        // TODO: Implement listChildRules() method.
    }

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {
        // TODO: Implement apply() method.
    }
}