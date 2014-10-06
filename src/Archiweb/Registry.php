<?php


namespace Archiweb;


use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\SaveQueryContext;
use Archiweb\Expression\NAryExpression;
use Archiweb\Operator\AndOperator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Registry {

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

        $saveQueryContext = new SaveQueryContext($this->appCtx, $model);

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
            $this->queryBuilder->from($this->realModelClassName($entity), lcfirst($entity));
        }

        return $this->queryBuilder;

    }

    /**
     * @param string $entity
     *
     * @return string
     */
    private static function realModelClassName ($entity) {

        $class = '\Archiweb\Model\\' . $entity;
        if (!class_exists($class)) {
            throw new \RuntimeException('entity not found');
        }

        return $class;

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
     */
    public function find (FindQueryContext $ctx, $hydrateArray = true) {

        $entity = $ctx->getEntity();
        $class = self::realModelClassName($entity);

        $qb = $this->getQueryBuilder($ctx->getEntity());
        $alias = lcfirst($entity);

        $keyPaths = $ctx->getKeyPaths();
        if (empty($keyPaths)) {
            throw new \RuntimeException('fields are required');
        }



        foreach ($keyPaths as $keyPath) {
            $field = $keyPath->resolve($this, $ctx);
            $keyPathField = $keyPath->getField($ctx);
            if (is_a($keyPathField, '\Archiweb\Field\StarField')) {
                $qb->addSelect($field);
            }
            else {
                $qb->addSelect($field);
            }
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

}