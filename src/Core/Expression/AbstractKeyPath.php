<?php


namespace Core\Expression;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Registry;
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
     * @var string
     */
    protected $result;

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

        if ($this->result) {
            return $this->result;
        }

        if (!$this->field) {
            $this->process($ctx);
        }

        $aliasForEntity = $registry->findAliasForEntity($this->getEntity($ctx));
        if (count($aliasForEntity) == 0) {
            throw new \RuntimeException('alias for entity ' . $this->getEntity($ctx) . ' not found');
        }
        elseif (count($aliasForEntity) > 1) {
            throw new \RuntimeException('more than one alias found for entity ' . $this->getEntity($ctx));
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
            return $this->result = $prevAlias . '.' . $joinToDo['field'];
        }

        return $this->result = $alias . ($this->field == '*' ? '' : ('.' . $this->field));

    }

    /**
     * @param FindQueryContext $ctx
     */
    public function process (FindQueryContext $ctx) {

        $exploded = explode('.', $this->getValue());

        $entity = Registry::realModelClassName($this->getEntity($ctx));

        for ($i = 0; $i < count($exploded); ++$i) {

            $isLast = $i + 1 == count($exploded);

            $field = $exploded[$i];

            if ($field == '*') {
                break;
            }

            $metadata = ApplicationContext::getInstance()->getClassMetadata($entity);
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
     * @return \Core\Field\Field
     */
    public function getField (FindQueryContext $ctx = NULL) {

        if (!$this->field) {
            if (is_null($ctx)) {
                throw new \RuntimeException('try to fetch field of a non processed KeyPath without context');
            }
            $this->process($ctx);
        }

        if ($this->field == '*') {
            return new StarField($this->entity);
        }
        else {
            return new Field($this->entity, $this->field);
        }

    }

    /**
     * @param string $entity
     */
    public function setRootEntity ($entity) {

        $this->rootEntity = $entity;

    }

}