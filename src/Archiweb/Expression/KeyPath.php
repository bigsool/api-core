<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Field\Field;
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
     * @var string[]
     */
    protected $joinsToDo = [];

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!self::isValidKeyPath($value)) {
            throw new \RuntimeException('invalid KeyPath');
        }

        parent::__construct($value);

    }

    /**
     * @param string $value
     * return boolean
     */
    static public function isValidKeyPath($value) {

        if (!is_string($value) || (!preg_match('/^[a-zA-Z]+(\.[a-zA-Z]+)*(\.\*)?$/', $value) && $value != '*')) {
            return false;
        }

        return true;

    }

    /**
     * @return string|void
     */
    public function resolve (Registry $registry, QueryContext $ctx) {

        if (!($ctx instanceof FindQueryContext)) {
            throw new \RuntimeException('invalid context');
        }

        if (!$this->field) {
            $this->process($ctx);
        }

        $alias = lcfirst($ctx->getEntity());
        foreach ($this->joinsToDo as $joinToDo) {
            $alias = $registry->addJoin($ctx, $alias, $joinToDo);
        }

        return $alias . ($this->field == '*' ? '' : ('.' . $this->field));

    }

    /**
     * @param FindQueryContext $ctx
     */
    public function process (FindQueryContext $ctx) {

        $exploded = explode('.', $this->getValue());
        $entity = '\Archiweb\Model\\' . $ctx->getEntity();

        for ($i = 0; $i < count($exploded); ++$i) {

            $isLast = $i + 1 == count($exploded);

            $field = $exploded[$i];

            if ($field == '*') {
                break;
            }

            $metadata = $ctx->getApplicationContext()->getClassMetadata($entity);
            $fields = $metadata->getFieldNames();

            if (in_array($field, $fields)) {
                if (!$isLast) {
                    throw new \RuntimeException("$field is a field, not an entity");
                }
                $this->entity = $this->getEntityForClass($entity);
                $this->field = $field;

                return;
            }

            $associations = $metadata->getAssociationNames();

            if (in_array($field, $associations)) {
                $this->joinsToDo[] = $field;
                $entity = $metadata->getAssociationMapping($field)['targetEntity'];
            }
            else {
                throw new \RuntimeException("$field not found in $entity");
            }

        }

        $this->entity = $this->getEntityForClass($entity);
        $this->field = '*';

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
     * @param FindQueryContext $ctx
     *
     * @return \Archiweb\Field\Field
     */
    public function getField (FindQueryContext $ctx) {

        if (!$this->field) {
            $this->process($ctx);
        }

        return $ctx->getApplicationContext()->getFieldByEntityAndName($this->entity, $this->field);

    }

}