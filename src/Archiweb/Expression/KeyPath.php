<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Field;
use Archiweb\Registry;

class KeyPath extends Value {

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var FindQueryContext
     */
    protected $ctx;

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!is_string($value)) {
            throw new \RuntimeException('invalid type');
        }
        if (!preg_match('/^[a-zA-Z_0-9]+(\.[a-zA-Z_0-9]+)*(\.\*)?$/', $value) && $value != '*') {
            throw new \RuntimeException('invalid format');
        }
        parent::__construct($value);
    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        if (!($context instanceof FindQueryContext)) {
            throw new \RuntimeException('invalid context');
        }

        $this->ctx = $context;

        $exploded = explode('.', $this->getValue());
        $entity = '\Archiweb\Model\\' . $context->getEntity();
        $alias = lcfirst($context->getEntity());

        for ($i = 0; $i < count($exploded); ++$i) {

            $isLast = $i + 1 == count($exploded);

            $field = $exploded[$i];

            if ($field == '*') {
                if (!$isLast) {
                    throw new \RuntimeException("* must be at the end of a keyPath");
                }
                break;
            }

            $metadata = $context->getApplicationContext()->getClassMetadata($entity);
            $fields = $metadata->getFieldNames();

            if (in_array($field, $fields)) {
                if (!$isLast) {
                    throw new \RuntimeException("$field is a field, not an entity");
                }
                $this->entity = $this->getEntityForClass($entity);
                $this->field = $field;

                return $alias . '.' . $field;
            }

            $associations = $metadata->getAssociationNames();

            if (in_array($field, $associations)) {
                $alias = $registry->addJoin($context, $alias, $field);
                $entity = $metadata->getAssociationMapping($field)['targetEntity'];
            }
            else {
                throw new \RuntimeException("$field not found in $entity");
            }

        }

        $this->entity = $this->getEntityForClass($entity);
        $this->field = '*';

        return $alias;

    }

    /**
     * @param string $class
     *
     * @return string
     */
    protected function getEntityForClass ($class) {

        return (new \ReflectionClass($class))->getShortName();
    }

    /**
     * @return Field
     */
    public function getField () {

        return $this->ctx->getApplicationContext()->getFieldByEntityAndName($this->entity, $this->field);
        
    }

}