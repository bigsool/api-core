<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
use Archiweb\Filter\Filter;

class FieldRule implements Rule {

    /**
     * @var \Archiweb\Field\Field
     */
    protected $field;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param Field  $field
     * @param Filter $filter
     */
    public function __construct (Field $field, Filter $filter) {

        $this->field = $field;
        $this->filter = $filter;

    }

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {

        if (!($ctx instanceof FindQueryContext)) {
            throw new \RuntimeException('FieldRule is incompatible with SaveContext');
        }

        $ctx->addFilter($this->getFilter());
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

        return [];

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

        $keyPaths = $ctx->getKeyPaths();
        foreach ($keyPaths as $keyPath) {
            $field = $keyPath->getField($ctx);
            if ($field->getEntity() != $this->getField()->getEntity()) {
                continue;
            }
            if ($field instanceof StarField || $this->getField() instanceof StarField
                || $field->getName() == $this->getField()->getName()
            ) {
                return true;
            }
        }

        return false;

    }

    /**
     * @return Filter
     */
    public function getFilter () {

        return $this->filter;

    }

    /**
     * @return \Archiweb\Field\Field
     */
    public function getField () {

        return $this->field;

    }

}