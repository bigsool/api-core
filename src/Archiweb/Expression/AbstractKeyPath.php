<?php


namespace Archiweb\Expression;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Registry;
use Doctrine\ORM\Query;

abstract class AbstractKeyPath extends Value {

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $rootEntity;

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

        $aliasForEntity = $registry->findAliasForEntity($this->getEntity($ctx));
        if (count($aliasForEntity) == 0) {
            throw new \RuntimeException('alias for entity ' . $this->getEntity($ctx) . ' not found');
        }
        elseif (count($aliasForEntity) > 1) {
      //      throw new \RuntimeException('more than one alias found for entity ' . $this->getEntity($ctx));
        }

        $alias = $aliasForEntity[0];
        $prevAlias = NULL;
        foreach ($this->joinsToDo as $joinToDo) {
            $prevAlias = $alias;
            $alias =
                $registry->addJoin($ctx, $alias, $joinToDo['field'], $this->getEntityForClass($joinToDo['entity']),
                                   $this->useLeftJoin);
        }

        if ($this->isUsedInExpression() && isset($prevAlias) && $this->field == '*') {
            return $prevAlias . '.' . $joinToDo['field'];
        }

        return $alias . ($this->field == '*' ? '' : ('.' . $this->field));

    }

    /**
     * @param FindQueryContext $ctx
     */
    public function process (FindQueryContext $ctx) {

        $exploded = explode('.', $this->getValue());

        $entity = '\Archiweb\Model\\' . $this->getEntity($ctx);

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
     * @param QueryContext $ctx
     *
     * @return string
     */
    protected function getEntity (QueryContext $ctx) {

        return isset($this->rootEntity) ? $this->rootEntity : $ctx->getEntity();

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
     * @return bool
     */
    protected abstract function isUsedInExpression ();

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

    /**
     * @param string $entity
     */
    public function setRootEntity ($entity) {

        $this->rootEntity = $entity;

    }

}