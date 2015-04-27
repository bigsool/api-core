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
use Core\Util\ModelConverter;
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
    protected $entityManager;

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var string[]
     */
    protected $joins = [];

    /**
     * @var string[][]
     */
    protected $aliasForEntity = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param EntityManager      $entityManager
     * @param ApplicationContext $ctx
     */
    public function __construct (EntityManager $entityManager, ApplicationContext $ctx) {

        $this->entityManager = $entityManager;
        $this->appCtx = $ctx;

    }

    /**
     * @return string
     */
    public static function getLastExecutedQuery () {

        return self::$dql;

    }

    /**
     * @return \string[]
     */
    public function getJoins () {

        return $this->joins;
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
        $this->entityManager->persist($model);
        $this->entityManager->flush();

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

        // TODO: even if a left join was done, we should replace it by a inner join if it is
        if (!isset($this->joins[$join])) {

            $newAlias = $alias . ucfirst($field);
            $ctx->addJoinedEntity($entity);
            $joinMethod = $useLeftJoin ? 'leftJoin' : 'innerJoin';
            $this->getQueryBuilder($ctx->getEntity())->$joinMethod($join, $newAlias);

            // TODO: if a LeftJoin was introduce and now we wanna do a innerJoin we should add a condition (IS NOT NULL)

            $this->joins[$join] = $newAlias;
            $this->addAliasForEntity($entity, $newAlias);

        }

        return $this->joins[$join];

    }

    /**
     * @param string $entity
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder ($entity) {

        if (!isset($this->queryBuilder)) {
            $this->queryBuilder = $this->entityManager->createQueryBuilder();
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
            $class = '\Core\Model\\' . $entity;
            if (!class_exists($class)) {
                throw new \RuntimeException('entity not found');
            }
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
     * @param bool             $hydrateArray
     *
     * @return array
     * @throws Error\FormattedError
     */
    public function find (FindQueryContext $ctx, $hydrateArray = true) {

        $entity = $ctx->getEntity();

        $qb = $this->getQueryBuilder($entity);

        /**
         * @var RelativeField[] $relativeFields
         */
        $relativeFields = array_merge($ctx->getFields(), $ctx->getReqCtx()->getFormattedReturnedFields());

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
            foreach ($fields as $field) {
                //$exploded = explode('.', $field);
                if ($resolvableField instanceof Aggregate
                    || $resolvableField->getResolvedField() == '*'
                ) {
                    if ($resolvableField->getAlias()) {
                        $field .= ' AS ' . $resolvableField->getAlias();
                    }
                    $qb->addSelect($field);
                }/*
                else {
                    if (count($exploded) == 2) {
                        if (!$hydrateArray) {
                            throw new \RuntimeException('cannot do partial object with Object Hydration');
                        }
                        $entities[$exploded[0]][] = $exploded[1];
                    }
                }*/
            }

        }

        /*
        foreach ($entities as $entity => $fields) {
            $selectClause = 'partial ' . $entity . '.{';
            if (!in_array('id', $fields)) {
                $selectClause .= 'id,';
            }
            $selectClause .= implode(',', $fields);
            $selectClause .= '}';
            $qb->addSelect($selectClause);
        }*/

        $needGroupByClause = false;
        $groupByClause = "";

        foreach ($resolvableFields as $resolvableField) {
            if ($resolvableField instanceof Aggregate) {
                if (count($resolvableFields) > 1) {
                    $needGroupByClause = true;
                }
                continue;
            }
            $groupByClause .= implode(',', $resolvableField->resolve($this, $ctx)) . ',';
        }

        $groupByClause = substr($groupByClause, 0, strlen($groupByClause) - 1);

        if ($needGroupByClause) {
            $qb->addGroupBy($groupByClause);
        }

        $ruleProcessor = new Processor();
        $ruleProcessor->apply($ctx);

        $auth = $ctx->getReqCtx()->getAuth();
        $login = NULL;
        if (isset($auth) && $credential = $auth->getCredential()) {
            $login = $credential->getLogin();
        }
        $ctx->setParam('__LOGIN__', $login);

        $expressions = [];
        foreach ($ctx->getFilters() as $filter) {
            $expressions[] = $filter->getExpression();
        }
        if ($expressions) {
            $expression = new NAryExpression(new AndOperator(), $expressions);
            $qb->andWhere($expression->resolve($this, $ctx));
        }
        $qb->setParameters($this->params);

        $query = $qb->getQuery();
        self::$dql = $query->getDQL();

        $result = $query->getResult(/*$hydrateArray ? Query::HYDRATE_ARRAY :*/
            'RestrictedObjectHydrator');

        if ($hydrateArray) {
            $requestedFields = [];
            foreach ($relativeFields as $relativeField) {
                $requestedFields[] = $relativeField->getAlias() ?: $relativeField->getValue();
            }
            foreach ($result as &$data) {
                if (is_array($data)) {
                    foreach ($data as &$object) {
                        if (is_object($object)) {
                            $object = (new ModelConverter())->toArray($object, $requestedFields);
                        }
                    }
                }
                else {
                    $data = (new ModelConverter())->toArray($data, $requestedFields);
                }
            }
        }

        //$result = $this->insertCalculatedFields($hydrateArray, $resolvableFields, $result);

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

        /*
        $relativeFieldsToAdd = [];
        foreach ($relativeFields as $relativeField) {
            $tmpResolvableFields = $relativeField->resolve($this, $ctx);
            if ($relativeField->getResolvedField() != '*') {
                foreach ($tmpResolvableFields as $tmpResolvableField) {
                    if (!($tmpResolvableField instanceof RealField)) {
                        continue 2;
                    }
                }
                $fieldValue = $relativeField->getValue();
                $prefix = substr($fieldValue, 0, strrpos($fieldValue, '.'));
                $newFieldValue = $prefix ? $prefix . '.*' : '*';
                $relativeFieldsToAdd[] = new RelativeField($newFieldValue);
            }
        }
        $relativeFields = array_merge($relativeFields, $relativeFieldsToAdd);

        return $relativeFields;*/
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

    public function delete ($model) {

        $this->entityManager->remove($model);
        $this->entityManager->flush();

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
     * @param string|object $classOrObject
     *
     * @return boolean
     */
    public function isEntity ($classOrObject) {

        if (is_object($classOrObject)) {
            $classOrObject = ClassUtils::getClass($classOrObject);
        }

        return !$this->entityManager->getMetadataFactory()->isTransient($classOrObject);

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
                    $resolvableField->exec($data);
                }

            }

        }

        return $result;
    }

}