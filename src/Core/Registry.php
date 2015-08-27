<?php


namespace Core;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\SaveQueryContext;
use Core\Expression\NAryExpression;
use Core\Field\Aggregate;
use Core\Field\CalculatedField;
use Core\Field\RealField;
use Core\Field\RelativeField;
use Core\Field\ResolvableField;
use Core\Module\MagicalEntity;
use Core\Operator\AndOperator;
use Core\Parameter\UnsafeParameter;
use Core\Rule\Processor;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Registry implements EventSubscriber {

    /**
     * @var string
     */
    protected static $dql = '';

    /**
     * @var EntityManager
     */
    protected static $entityManager;

    /**
     * @var ApplicationContext
     */
    protected static $appCtx;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var string[]
     */
    protected $leftJoins = [];

    /**
     * @var string[]
     */
    protected $innerJoins = [];

    /**
     * @var string[][]
     */
    protected $aliasForEntity = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     *
     */
    public function __construct () {

        static::$entityManager->getEventManager()->addEventSubscriber($this);

    }

    /**
     * @param EntityManager $entityManager
     */
    public static function setEntityManager (EntityManager $entityManager) {

        self::$entityManager = $entityManager;

    }

    /**
     * @param ApplicationContext $appCtx
     */
    public static function setApplicationContext (ApplicationContext $appCtx) {

        self::$appCtx = $appCtx;

    }

    /**
     * @return string
     */
    public static function getLastExecutedQuery () {

        return self::$dql;

    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function save ($model) {

        if ($model instanceof MagicalEntity) {
            $model = $model->getMainEntity();
        }

        $saveQueryContext = new SaveQueryContext($model);

        $ruleProcessor = new Processor();
        $ruleProcessor->apply($saveQueryContext);
        static::$entityManager->persist($model);
        static::$entityManager->flush();

    }

    /**
     * @param FindQueryContext $ctx
     * @param string           $alias
     * @param string           $field
     * @param string           $entity
     * @param bool             $useLeftJoin
     *
     * @return string
     */
    public function addJoin (FindQueryContext $ctx, $alias, $field, $entity, $useLeftJoin = false) {

        $join = $alias . '.' . $field;
        $newAlias = $alias . ucfirst($field);

        // if inner join
        if (!$useLeftJoin) {

            // remove from left joins
            if (array_key_exists($join, $this->leftJoins)) {
                unset($this->leftJoins[$join]);
            }

            // add in inner joins
            $this->innerJoins[$join] = $newAlias;

        }
        // if left join
        else {

            if (!array_key_exists($join, $this->innerJoins) && !array_key_exists($join, $this->leftJoins)) {
                $this->leftJoins[$join] = $newAlias;
            }
        }

        $this->addAliasForEntity($entity, $newAlias);

        return $newAlias;

    }

    /**
     * @return \string[]
     */
    public function getInnerJoins () {

        return $this->innerJoins;

    }

    /**
     * @return \string[]
     */
    public function getLeftJoins () {

        return $this->leftJoins;

    }

    /**
     * @return \string[]
     */
    public function getJoins () {

        return array_merge($this->getInnerJoins(), $this->getLeftJoins());

    }

    /**
     * @param FindQueryContext $ctx
     */
    protected function doJoins (FindQueryContext $ctx) {

        $joins = [];
        foreach ($this->getLeftJoins() as $join => $alias) {
            $joins[$join] = [$alias, true];
        }
        foreach ($this->getInnerJoins() as $join => $alias) {
            $joins[$join] = [$alias, false];
        }

        // sort joins otherwise you will have this error
        // [Semantical Error] near '.user userCredentialUser':
        // Error:Identification Variable userCredential used in join path expression but was not defined before.
        uksort($joins, function ($joinA, $joinB) {

            $diffDots = substr_count($joinA, '.') - substr_count($joinB, '.');

            return $diffDots != 0 ? $diffDots : strlen($joinA) - strlen($joinB);

        });

        $queryBuilder = $this->getQueryBuilder($ctx->getEntity());

        foreach ($joins as $join => list($alias, $isLeftJoin)) {
            if ($isLeftJoin) {
                $queryBuilder->leftJoin($join, $alias);
            }
            else {
                $queryBuilder->innerJoin($join, $alias);
            }
        }

    }

    /**
     * @param string $entity
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder ($entity) {

        if (!isset($this->queryBuilder)) {
            $this->queryBuilder = static::$entityManager->createQueryBuilder();
            $alias = lcfirst($entity);
            $this->queryBuilder->from($this->realModelClassName($entity), $alias);
            $this->addAliasForEntity($entity, $alias);
        }

        return $this->queryBuilder;

    }

    /**
     * @param string $entity
     *
     * @return string
     */
    public static function realModelClassName ($entity) {

        $product = ApplicationContext::getInstance()->getProduct();

        $class = '\\' . $product . '\Model\\' . $entity;
        if (!class_exists($class)) {
            throw new \RuntimeException('entity not found');
        }

        return $class;

    }

    /**
     * @param string $entity
     * @param string $alias
     */
    protected function addAliasForEntity ($entity, $alias) {

        if (!isset($this->aliasForEntity[$entity])) {
            $this->aliasForEntity[$entity] = [];
        }
        if (!in_array($alias, $this->aliasForEntity[$entity])) {
            $this->aliasForEntity[$entity][] = $alias;
        }

    }

    /**
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter ($parameter, $value) {

        $this->params[$parameter] = $value;

    }

    /**
     * @param FindQueryContext $ctx
     *
     * @return array
     *
     */
    public function find (FindQueryContext $ctx) {

        $entity = $ctx->getEntity();

        $qb = $this->getQueryBuilder($entity);

        $entityAliases = [];

        /**
         * @var RelativeField[] $relativeFields
         */
        $relativeFields = array_merge($ctx->getFields(), $ctx->getRequestContext()->getFormattedReturnedFields());

        $resolvableFields = $this->resolveRelativeFields($relativeFields, $ctx);

        // in case of object, we don't want to specify fields but entities
        //if (!$hydrateArray) {
        $resolvableFields = $this->addStarFields($ctx, $resolvableFields);
        //}

        // cleanup duplicated fields
        $resolvableFields = self::removeDuplicatedFields($resolvableFields);

        // removed not necessary fields
        $resolvableFields = $this->removedNotNecessaryFields($resolvableFields, $ctx);

        if (empty($resolvableFields)) {
            throw new \RuntimeException('fields are required');
        }

        //$entities = [];
        foreach ($resolvableFields as $resolvableField) {
            $fields = $resolvableField->resolve($this, $ctx);
            if (in_array('Core\Expression\Resolver', class_uses($resolvableField))) {
                $resolvedEntity = $resolvableField->getResolvedEntity();
                if (!isset($entityAliases[$resolvedEntity])) {
                    $entityAliases[$resolvedEntity] = [];
                }
                $targetedEntityAlias = $resolvableField->getTargetedEntityAlias();
                if ($targetedEntityAlias) {
                    $entityAliases[$resolvedEntity][] = $targetedEntityAlias;
                }
            }
            foreach ($fields as $field) {
                //$exploded = explode('.', $field);
                if ($resolvableField instanceof Aggregate
                    || $resolvableField->getResolvedField() == '*'
                ) {
                    if ($resolvableField->getAlias()) {
                        $field .= ' AS ' . $resolvableField->getAlias();
                    }
                    $qb->addSelect($field);
                }
            }

        }

        $needGroupByClause = false;
        $groupByClauseFields = [];

        foreach ($resolvableFields as $resolvableField) {
            if ($resolvableField instanceof Aggregate) {
                if (count($resolvableFields) > 1) {
                    $needGroupByClause = true;
                }
                continue;
            }
            $groupByClauseFields = array_merge($groupByClauseFields, $resolvableField->resolve($this, $ctx));
        }

        $groupByClause = implode(',', $groupByClauseFields);

        if ($needGroupByClause) {
            $qb->addGroupBy($groupByClause);
        }

        $ruleProcessor = new Processor();
        $ruleProcessor->apply($ctx);

        $auth = $ctx->getRequestContext()->getAuth();
        $login = NULL;
        if (isset($auth) && $credential = $auth->getCredential()) {
            $login = $credential->getLogin();
        }
        $ctx->setParam('__LOGIN__', $login);

        $expressions = [];
        foreach ($ctx->getFilters() as $filter) {
            $filterEntity = $filter->getEntity();
            if ($filter->getAliasForEntityToUse()) {
                $expressions[] = $filter->getExpression();
            }
            else {
                if (!isset($entityAliases[$filterEntity]) || count($entityAliases[$filterEntity]) == 0) {
                    $expressions[] = $filter->getExpression();
                }
                else {
                    foreach ($entityAliases[$filterEntity] as $entityAlias) {
                        $filter = clone $filter;
                        $filter->setAliasForEntityToUse($entityAlias);
                        $expressions[] = $filter->getExpression();
                    }
                }
            }
        }
        if ($expressions) {
            $expression = new NAryExpression(new AndOperator(), $expressions);
            $qb->andWhere($expression->resolve($this, $ctx));
        }
        $qb->setParameters($this->params);

        $this->doJoins($ctx);

        $query = $qb->getQuery();
        self::$dql = $query->getDQL();

        // this will add related entity fields
        $query->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);

        $ctx->getApplicationContext()->getSQLLogger()->getMLogger()->addInfo($query->getDQL());

        $ctx->getApplicationContext()->getTraceLogger()->trace('before fetching from database');

        $result = $query->getResult('RestrictedObjectHydrator');

        $ctx->getApplicationContext()->getTraceLogger()->trace('entity hydrated');

        array_walk_recursive($result, function ($object) use ($ctx) {

            if (is_object($object) && Registry::isEntity($object)) {
                // TODO faire un setter sur les entity
                $refProp = new \ReflectionProperty($object, 'findQueryContext');
                $refProp->setAccessible(true);
                $refProp->setValue($object, $ctx);
            }
        });

        return $result;

    }

    /**
     * @param RelativeField[]  $relativeFields
     * @param FindQueryContext $ctx
     *
     * @return ResolvableField[]
     */
    protected function resolveRelativeFields (array $relativeFields, FindQueryContext $ctx) {

        $resolvableFields = [];
        foreach ($relativeFields as $relativeField) {
            $tmpResolvableFields = $relativeField->resolve($this, $ctx);
            foreach ($tmpResolvableFields as $resolvableField) {
                // Field as to be resolve to do the isEqual()
                $resolvableField->resolve($this, $ctx);
            }
            $resolvableFields = array_merge($resolvableFields, $tmpResolvableFields);
        }

        return $resolvableFields;

    }

    /**
     * @param FindQueryContext  $ctx
     * @param ResolvableField[] $resolvableFields
     *
     * @return ResolvableField[]
     */
    protected function addStarFields (FindQueryContext $ctx, $resolvableFields) {

        $resolvableFieldsToAdd = [];
        foreach ($resolvableFields as $resolvableField) {
            if ($resolvableField instanceof RealField && $resolvableField->getResolvedField() != '*') {
                $fieldValue = $resolvableField->getValue();
                $prefix = substr($fieldValue, 0, strrpos($fieldValue, '.'));
                $newFieldValue = $prefix ? $prefix . '.*' : '*';
                $resolvableFieldsToAdd[] = $resolvableFieldToAdd = new RealField($newFieldValue);
                $resolvableFieldToAdd->resolve($this, $ctx);
            }
        }

        $resolvableFields = array_merge($resolvableFields, $resolvableFieldsToAdd);

        return $resolvableFields;
    }

    /**
     * @param ResolvableField[] $resolvableFields
     *
     * @return ResolvableField[]
     */
    public static function removeDuplicatedFields (array &$resolvableFields) {

        return array_filter($resolvableFields,
            function (ResolvableField &$currentResolvableField) {

                /**
                 * @var ResolvableField[] $resolvableFields
                 */
                static $resolvableFields = [];

                foreach ($resolvableFields as $resolvableField) {
                    if ($resolvableField->isEqual($currentResolvableField)) {
                        return false;
                    }
                }

                $resolvableFields[] = $currentResolvableField;

                return true;

            }
        );
    }

    /**
     * @param ResolvableField[] $resolvableFields
     * @param FindQueryContext  $ctx
     *
     * @return ResolvableField[]
     */
    protected function removedNotNecessaryFields (array $resolvableFields, FindQueryContext $ctx) {

        $finalResolvableFields = [];
        /**
         * @var ResolvableField[] $resolvableFields
         */
        $resolvableFields = array_values($resolvableFields);
        for ($i = 0; $i < count($resolvableFields); ++$i) {
            $resolvableField = $resolvableFields[$i];
            if (!($resolvableField instanceof RealField)) {
                $finalResolvableFields[] = $resolvableField;
                continue;
            }
            $resolvedValue = $resolvableField->resolve($this, $ctx)[0];
            $explodedResolvedValue = explode('.', $resolvedValue);
            $entityPath = $explodedResolvedValue[0];
            $field = count($explodedResolvedValue) == 2 ? $explodedResolvedValue[1] : '*';
            if (array_key_exists($entityPath, $finalResolvableFields)) {
                continue;
            }
            if ($field == '*') {
                $finalResolvableFields[$entityPath] = $resolvableField;
                continue;
            }
            for ($j = $i + 1; $j < count($resolvableFields); ++$j) {
                $_resolvableField = $resolvableFields[$j];
                if (!($_resolvableField instanceof RealField)) {
                    continue;
                }
                $_resolvedValue = $_resolvableField->resolve($this, $ctx)[0];
                $_explodedResolvedValue = explode('.', $_resolvedValue);
                $_entityPath = $_explodedResolvedValue[0];
                $_field = count($_explodedResolvedValue) == 2 ? $_explodedResolvedValue[1] : '*';
                if ($_entityPath != $entityPath) {
                    continue;
                }
                if ($_field == '*') {
                    continue 2;
                }
            }
            $finalResolvableFields[] = $resolvableField;
        }

        return array_values($finalResolvableFields);
    }

    /**
     * @param string|object $classOrObject
     *
     * @return boolean
     */
    public function isEntity ($classOrObject) {

        if (is_object($classOrObject)) {
            $classOrObject = ClassUtils::getClass($classOrObject);
        }

        return !static::$entityManager->getMetadataFactory()->isTransient($classOrObject);

    }

    public function delete ($model) {

        if ($model instanceof MagicalEntity) {
            $model = $model->getMainEntity();
        }

        static::$entityManager->remove($model);
        static::$entityManager->flush();

    }

    /**
     * @param string $entity
     *
     * @return string[]
     */
    public function findAliasForEntity ($entity) {

        return isset($this->aliasForEntity[$entity]) ? $this->aliasForEntity[$entity] : [];

    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents () {

        return [Events::prePersist, Events::preUpdate];

    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist (LifecycleEventArgs $args) {

        $this->preModification($args);

    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function preModification (LifecycleEventArgs $args) {

        $entity = $args->getEntity();
        $fields = $args->getEntityManager()->getClassMetadata(get_class($entity))->getFieldNames();

        foreach ($fields as $fieldName) {
            $getter = 'get' . ucfirst($fieldName);
            $value = $entity->$getter();
            if ($value instanceof UnsafeParameter) {
                throw new \RuntimeException('unsafe parameter ' . $fieldName . ' detected');
            }
        }

    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate (LifecycleEventArgs $args) {

        $this->preModification($args);

    }

    /**
     * @param bool              $hydrateArray
     * @param ResolvableField[] $resolvableFields
     * @param array             $result
     *
     * @return mixed
     */
    protected function insertCalculatedFields ($hydrateArray, $resolvableFields, $result) {

        if ($hydrateArray) {

            foreach ($resolvableFields as $resolvableField) {

                if (!($resolvableField instanceof CalculatedField)) {
                    continue;
                }

                foreach ($result as &$data) {
                    $resolvableField->execute($data);
                }

            }

        }

        return $result;
    }

}