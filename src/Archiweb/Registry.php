<?php


namespace Archiweb;


use Archiweb\Context\FindQueryContext;
use Archiweb\Expression\NAryExpression;
use Archiweb\Operator\AndOperator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Registry {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var string[]
     */
    protected $joins = [];

    /**
     * @param EntityManager $entityManager
     */
    public function __construct (EntityManager $entityManager) {

        $this->entityManager = $entityManager;

    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function save ($model) {

        $this->entityManager->persist($model);
        $this->entityManager->flush();

    }

    /**
     * @param $alias
     * @param $field
     *
     * @return string
     */
    public function addJoin (FindQueryContext $ctx, $alias, $field) {

        $join = $alias . '.' . $field;

        if (!isset($this->joins[$join])) {

            $newAlias = $alias . ucfirst($field);
            $this->getQueryBuilder($ctx->getEntity())->innerJoin($join, $newAlias);

            $this->joins[$join] = $newAlias;

        }

        return $this->joins[$join];

    }

    /**
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
        // TODO: Implement setParameter() method
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
            // TODO: apply rule
            $keyPathAlias = $keyPath->resolve($this, $ctx);
            $keyPathField = $keyPath->getField($ctx);
            if (is_a($keyPathField, '\Archiweb\StarField')) {
                $qb->addSelect($keyPathAlias);
            }
            else {
                $qb->addSelect($keyPathAlias . '.' . $keyPathField->getName());
            }
        }


        $expressions = [];
        foreach ($ctx->getFilters() as $filter) {
            $expressions[] = $filter->getExpression();
        }
        if ($expressions) {
            $expression = new NAryExpression(new AndOperator(), $expressions);
            $qb->andWhere($expression->resolve($this, $ctx));
        }

        $query = $qb->getDQL();

        return $qb->getQuery()->getResult($hydrateArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);

    }

}