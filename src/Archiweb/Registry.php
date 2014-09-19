<?php


namespace Archiweb;


use Archiweb\Parameter\Parameter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class Registry {

    /**
     * @var ActionContext
     */
    protected $context;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param ActionContext $context
     */
    public function __construct (ActionContext $context) {

        $this->context = $context;
        $this->em = $context->getEntityManager();

    }

    /**
     * @param string      $entity
     * @param Parameter[] $params
     *
     * @return Object
     */
    public function create ($entity, array $params) {

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
    public function find ($entity) {

        $class = self::realModelClassName($entity);

        $qb = $this->em->createQueryBuilder();
        $qb->from($class, lcfirst($entity));

        return $qb;

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

} 