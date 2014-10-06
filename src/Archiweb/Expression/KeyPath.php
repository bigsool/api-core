<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
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
     * @var bool
     */
    protected $useLeftJoin;

    /**
     * @var string[]
     */
    protected $joinsToDo = [];

    /**
     * @param mixed $value
     * @param bool  $useLeftJoin
     */
    public function __construct ($value, $useLeftJoin = false) {

        if (!self::isValidKeyPath($value)) {
            throw new \RuntimeException('invalid KeyPath');
        }

        parent::__construct($value);

        $this->useLeftJoin = $useLeftJoin;

    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    public static function isValidKeyPath ($value) {

        if (!is_string($value) || (!preg_match('/^[a-zA-Z]+(\.[a-zA-Z]+)*(\.\*)?$/', $value) && $value != '*')) {
            return false;
        }

        return true;

    }

    /**
     * @param Registry     $registry
     * @param QueryContext $ctx
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $ctx) {

        if (!($ctx instanceof FindQueryContext)) {
            throw new \RuntimeException('invalid context');
        }

        if (!$this->field) {
            $this->process($ctx);
        }

        $alias = lcfirst($ctx->getEntity());
        $prevAlias = NULL;
        foreach ($this->joinsToDo as $joinToDo) {
            $prevAlias = $alias;
            $alias = $registry->addJoin($ctx, $alias, $joinToDo['field'], $joinToDo['entity'], $this->useLeftJoin);
        }

        if (isset($prevAlias) && $this->field == '*') {
            return $prevAlias . '.' . $joinToDo['field'];
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
                $entity = $metadata->getAssociationMapping($field)['targetEntity'];
                $this->joinsToDo[] = ['field' => $field, 'entity' => $entity];
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