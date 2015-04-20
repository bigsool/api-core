<?php


namespace Core\Rule;


use Core\Auth;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Filter\Filter;

class FieldRule implements Rule {

    /**
     * @var \Core\Field\Field
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

        if (is_null($ctx->getReqCtx()->getAuth())) {
            throw new \Exception;
        }
        // Do not apply rule if the Query is defined as INTERNAL
        if ($ctx->getReqCtx()->getAuth()->hasRights(Auth::INTERNAL)) {
            return false;
        }

        $keyPaths = $ctx->getReqCtx()->getFormattedReturnedFields();

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
     * @return \Core\Field\Field
     */
    public function getField () {

        return $this->field;

    }

}