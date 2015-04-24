<?php


namespace Core\Expression;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Registry;

trait Resolver {

    /**
     * @var array[]
     */
    protected $joinsToDo = [];

    /**
     * @var string
     */
    protected $resolvedField;

    /**
     * @var string
     */
    protected $resolvedEntity;

    /**
     * @var string|NULL
     */
    protected $aliasForEntityToUse;

    /**
     * @return NULL|string
     */
    public function getAliasForEntityToUse () {

        return $this->aliasForEntityToUse;
    }

    /**
     * @param NULL|string $aliasForEntityToUse
     */
    public function setAliasForEntityToUse ($aliasForEntityToUse) {

        $this->aliasForEntityToUse = $aliasForEntityToUse;
    }

    /**
     * @return string
     */
    public function getResolvedField () {

        return $this->resolvedField;
    }

    /**
     * @return string
     */
    public function getResolvedEntity () {

        return $this->resolvedEntity;
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

        //if (!$this->field) {
        $this->process($ctx);
        //}

        $aliasForEntity = $registry->findAliasForEntity($this->getEntity($ctx));
        if (!is_null($this->aliasForEntityToUse)) {
            if (!in_array($this->aliasForEntityToUse, $aliasForEntity)) {
                throw new \RuntimeException('alias not found');
            }
            $alias = $this->aliasForEntityToUse;
        }
        else {
            if (count($aliasForEntity) == 0) {
                throw new \RuntimeException('alias for entity ' . $this->getEntity($ctx) . ' not found');
            }
            elseif (isset($this->rootEntity) && count($aliasForEntity) > 1) {
                throw new \RuntimeException('more than one alias found for entity ' . $this->getEntity($ctx));
            }
            $alias = $aliasForEntity[0];
        }
        $prevAlias = NULL;
        $joinToDo = [];
        foreach ($this->joinsToDo as $joinToDo) {
            $prevAlias = $alias;
            $alias =
                $registry->addJoin($ctx, $alias, $joinToDo['field'], $this->getEntityForClass($joinToDo['entity']),
                                   $this->shouldUseLeftJoin());
        }

        if ($this->shouldResolveForAWhere() && isset($prevAlias) && $this->resolvedField == '*') {
            return $prevAlias . '.' . $joinToDo['field'];
        }

        return $alias . ($this->resolvedField == '*' ? '' : ('.' . $this->resolvedField));

    }

    /**
     * @param FindQueryContext $ctx
     *
     * @return bool
     * @throws \Doctrine\ORM\Mapping\MappingException
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
                $this->resolvedEntity = $this->getEntityForClass($entity);
                $this->resolvedField = $field;

                return true;
            }

            $associations = $metadata->getAssociationNames();

            if (in_array($field, $associations)) {
                $entity = $metadata->getAssociationMapping($field)['targetEntity'];
                $this->joinsToDo[implode('.', array_slice($exploded, 0, $i + 1))] =
                    ['field' => $field, 'entity' => $entity];
            }
            else {
                if ($this->shouldThrowExceptionIfFieldNotFound()) {
                    throw new \RuntimeException("$field not found in $entity");
                }

                $this->resolvedEntity = $this->getEntityForClass($entity);
                $this->resolvedField = $field;

                return false;
            }

        }

        $this->resolvedEntity = $this->getEntityForClass($entity);
        $this->resolvedField = '*';

        return true;

    }

    /**
     * @return string
     */
    public abstract function getValue ();

    /**
     * @param QueryContext $ctx
     *
     * @return string
     */
    public function getEntity (QueryContext $ctx) {

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
    public function shouldThrowExceptionIfFieldNotFound () {

        return true;

    }

    /**
     * @return bool
     */
    public function shouldUseLeftJoin () {

        return false;

    }

    /**
     * @return bool
     */
    public abstract function shouldResolveForAWhere ();

    /**
     * @param FindQueryContext $ctx
     *
     * @return \Core\Field\Field
     */
    public function getField (FindQueryContext $ctx = NULL) {

        if (!$this->resolvedField) {
            if (is_null($ctx)) {
                throw new \RuntimeException('try to fetch field of a non processed KeyPath without context');
            }
            $this->process($ctx);
        }

        if ($this->resolvedField == '*') {
            return new StarField($this->resolvedEntity);
        }
        else {
            return new Field($this->resolvedEntity, $this->resolvedField);
        }

    }

}