<?php


namespace Core;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\SaveQueryContext;
use Core\Expression\NAryExpression;
use Core\Module\MagicalEntity;
use Core\Operator\AndOperator;
use Core\Parameter\UnsafeParameter;
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
     * @param $model
     *
     * @return mixed
     */
    public function save ($model) {

        if ($model instanceof MagicalEntity) {
            $model = $model->getMainEntity();
        }

        $saveQueryContext = new SaveQueryContext($model);

        $ruleProcessor = new RuleProcessor();
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

        $class = '\\'.$product.'\Model\\' . $entity;
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
        $requestedEntity = $ctx->getReqCtx()->getReturnedRootEntity();

        if ($requestedEntity && $requestedEntity != $entity) {
            throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_BAD_ENTITY);
        }

        $qb = $this->getQueryBuilder($entity);


        $keyPaths = $ctx->getKeyPaths();

        // KeyPath as to be resolve to do the isEqual()
        foreach ($keyPaths as $keyPath) {
            $keyPath->resolve($this, $ctx);
        }

        foreach ($ctx->getReqCtx()->getReturnedKeyPaths() as $keyPathFromRequest) {
            // KeyPath as to be resolve to do the isEqual()
            $keyPathFromRequest->resolve($this, $ctx);
            foreach ($keyPaths as $alreadyAddedKeyPath) {
                if ($keyPathFromRequest->isEqual($alreadyAddedKeyPath)) {
                    continue 2;
                }
            }
            $keyPaths[] = $keyPathFromRequest;
        }
        if (empty($keyPaths)) {
            throw new \RuntimeException('fields are required');
        }

        // TODO: Implement partial objects
        // http://docs.doctrine-project.org/en/latest/reference/dql-doctrine-query-language.html#partial-object-syntax
        foreach ($keyPaths as $keyPath) {
            $field = $keyPath->resolve($this, $ctx);
            if ($keyPath->getAlias()) {
                $field .= ' AS ' . $keyPath->getAlias();
            }
            $qb->addSelect($field);
        }

        $ruleProcessor = new RuleProcessor();
        $ruleProcessor->apply($ctx);

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

        return $query->getResult($hydrateArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);

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