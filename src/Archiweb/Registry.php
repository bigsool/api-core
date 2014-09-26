<?php


namespace Archiweb;


use Archiweb\Context\FindQueryContext;
use Archiweb\Expression\NAryExpression;
use Archiweb\Operator\AndOperator;
use Archiweb\Parameter\Parameter;
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

        $entity = $qryCtx->getEntity();
        $params = $qryCtx->getParams();
        $class = self::realModelClassName($entity);

        $obj = new $class;
        $metadata = $qryCtx->getApplicationContext()->getEntityManager()->getClassMetadata($class);
        $fieldNames = $metadata->getFieldNames();

        foreach ($params as $key => $param) {
            if (!in_array($key, $fieldNames)) {
                throw new \RuntimeException("field {$key} not found in the model {$entity}");
            }
            if (!($param instanceof Parameter)) {
                throw new \RuntimeException('invalid type');
            }
            if (!$param->isSafe()) {
                throw new \RuntimeException("unsafe parameter currently not handled");
            }
            $methodName = 'set' . ucfirst($key);
            if (!method_exists($obj, $methodName)) {
                throw new \RuntimeException("method {$methodName} not found on the model {$entity}");
            }
            $obj->$methodName($param->getValue());
        }

        return $obj;

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
            $this->queryBuilder->from($entity, lcfirst($entity));
        }

        return $this->queryBuilder;

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
        $qb->from($class, $alias);

        $fields = $ctx->getFilters();
        if (empty($fields)) {
            throw new \RuntimeException('fields are required');
        }

        foreach ($fields as $field) {
            if (is_a($field, '\Archiweb\StarField')) {
                //TODO: s'il y a plusieurs entity comment préciser laquel on veut récupérer ?
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

        return $qb->getQuery()->getResult($hydrateArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);

    }

}