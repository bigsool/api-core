<?php


namespace Archiweb;


use Archiweb\Context\QueryContext;
use Archiweb\Expression\NAryExpression;
use Archiweb\Operator\AndOperator;
use Archiweb\Parameter\Parameter;
use Doctrine\ORM\QueryBuilder;

class Registry {

    /**
     * @param QueryContext $qryCtx
     *
     * @return Object|QueryBuilder
     */
    public function query (QueryContext $qryCtx) {

        // TODO: Implement query() method

    }

    /**
     * @param string $joinName
     *
     * @return string
     */
    public function addJoin ($joinName) {
        // TODO: Implement addJoin() method
    }

    /**
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter ($parameter, $value) {
        // TODO: Implement setParameter() method
    }

    /**
     * @param string      $entity
     * @param Parameter[] $params
     *
     * @return Object
     */
    protected function create ($entity, array $params) {

        $class = self::realModelClassName($entity);

        $obj = new $class;
        $metadata = $this->em->getClassMetadata($class);
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
     * @param string $entity
     *
     * @return QueryBuilder
     */
    protected function find ($entity) {

        $class = self::realModelClassName($entity);

        $qb = $this->em->createQueryBuilder();
        $alias = lcfirst($entity);
        $qb->from($class, $alias);

        $fields = $this->context->getFields();
        if (empty($fields)) {
            $qb->select($alias);
        }

        $expressions = [];
        foreach ($this->context->getFilters() as $filter) {
            $expressions[] = $filter->getExpression();
        }
        if ($expressions) {
            $expression = new NAryExpression(new AndOperator(), $expressions);
            $qb->andWhere($expression->resolve($this, $this->context));
        }

        return $qb;

    }

} 