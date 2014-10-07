<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;

class FieldRule implements Rule {

    /**
     * @var \Archiweb\Field\Field
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

        $this->field = $field;
        $this->rule = $rule;

    }

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {

        $this->getRule()->apply($ctx);

    }

    /**
     * @return string
     */
    public function getName () {

        return lcfirst($this->getField()->getEntity()) . ucfirst($this->getField()->getName()) . 'FieldRule';

    }

    /**
     * @return Rule[]
     */
    public function listChildRules () {

        return [$this->getRule()];

    }

    /**
     * @param QueryContext $ctx
     *
     * @return bool
     */
    public function shouldApply (QueryContext $ctx) {

        if (!($ctx instanceof FindQueryContext)) {
            return false;
        }


        if ($ctx->getEntity() != $this->getField()->getEntity() && !in_array( $this->getField()->getEntity() ,$ctx->getJoinedEntities())) {
            return false;
        }

        foreach ($ctx->getKeyPaths() as $keyPath) {
            $keyPathField = $keyPath->getField($ctx);
            if ($keyPathField->getEntity() != $ctx->getEntity() && !in_array($keyPathField->getEntity() ,$ctx->getJoinedEntities())) {
                continue;
            }
            if ($keyPathField instanceof StarField) {
                return $this->getRule()->shouldApply($ctx);
            }
            if ($keyPathField->getName() == $this->getField()->getName()) {
                return $this->getRule()->shouldApply($ctx);
            }
        }

        return false;

    }

    /**
     * @return Rule
     */
    public function getRule () {

        return $this->rule;

    }

    /**
     * @return \Archiweb\Field\Field
     */
    public function getField () {

        return $this->field;

    }

}