<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Registry;

class KeyPath extends Value {

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!is_string($value)) {
            throw new \RuntimeException('invalid type');
        }
        if (!preg_match('/^[a-zA-Z_0-9]+(\.[a-zA-Z_0-9]+)*$/', $value)) {
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


        $exploded = explode('.', $this->getValue());
        $entity = '\Archiweb\Model\\' . $context->getEntity();
        $alias = lcfirst($context->getEntity());

        for ($i = 0; $i < count($exploded); ++$i) {

            $field = $exploded[$i];
            $metadata = $context->getApplicationContext()->getClassMetadata($entity);

            $fields = $metadata->getFieldNames();

            if (in_array($field, $fields)) {
                if ($i + 1 != count($exploded)) {
                    throw new \RuntimeException("$field is a field, not an entity");
                }

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

        return $alias;

    }

}