<?php


namespace Core;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\SaveQueryContext;
use Core\Expression\NAryExpression;
use Core\Field\Aggregate;
use Core\Field\CalculatedField;
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
         * @var ResolvableField[] $resolvableFields
         */
        $resolvableFields = [];

        // Field as to be resolve to do the isEqual()
        foreach ($ctx->getFields() as $relativeField) {
            $tmpResolvableFields = $relativeField->resolve($this, $ctx);
            foreach ($tmpResolvableFields as $resolvableField) {
                $resolvableField->resolve($this, $ctx);
            }
            $resolvableFields = array_merge($resolvableFields, $tmpResolvableFields);
        }

        $reqCtxFields = $ctx->getReqCtx()->getFormattedReturnedFields();

        foreach ($reqCtxFields as $fieldFromRequest) {
            // Field as to be resolve to do the isEqual()
            $tmpResolvableFields = $fieldFromRequest->resolve($this, $ctx);
            foreach ($tmpResolvableFields as $resolvableField) {
                $resolvableField->resolve($this, $ctx);
            }
            $resolvableFields = array_merge($resolvableFields, $tmpResolvableFields);
        }

        // cleanup duplicated fields
        $resolvableFields = array_filter($resolvableFields,
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


        if (empty($resolvableFields)) {
            throw new \RuntimeException('fields are required');
        }

        // TODO: fix problem with partial objects
        // http://docs.doctrine-project.org/en/latest/reference/dql-doctrine-query-language.html#partial-object-syntax
        $entities = [];
        foreach ($resolvableFields as $resolvableField) {
            $fields = $resolvableField->resolve($this, $ctx);
            foreach ($fields as $field) {
                $exploded = explode('.', $field);
                if (!$hydrateArray || count($exploded) == 1 || $resolvableField instanceof Aggregate) {
                    if ($resolvableField->getAlias()) {
                        $field .= ' AS ' . $resolvableField->getAlias();
                    }
                    $qb->addSelect($field);
                }
                else {
                    if (count($exploded) == 2) {
                        $entities[$exploded[0]][] = $exploded[1];
                    }
                }
            }

        }

        foreach ($entities as $entity => $fields) {
            $selectClause = 'partial ' . $entity . '.{id,';
            $selectClause .= implode(',', $fields);
            $selectClause .= '}';
            $qb->addSelect($selectClause);
        }

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

        $result = $query->getResult($hydrateArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);

        if ($hydrateArray == Query::HYDRATE_ARRAY) {

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

}