<?php


namespace Core\Expression;


use Doctrine\ORM\Query;

abstract class AbstractKeyPath extends Value {

    use Resolver;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $rootEntity;

    /**
     * @var bool
     */
    protected $useLeftJoin;

    /**
     * @var string
     */
    protected $result;

    /**
     * @param mixed $value
     * @param bool  $useLeftJoin
     */
    public function __construct ($value, $useLeftJoin = false) {

        if (!self::isValidKeyPath($value)) {
            throw new \RuntimeException('invalid KeyPath');
        }

        parent::__construct($value);

        $this->useLeftJoin = $useLeftJoin;

    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    public static function isValidKeyPath ($value) {

        if (!is_string($value)
            || (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*([._][a-zA-Z][a-zA-Z0-9]*)*(\.\*)?$/', $value)
                && $value != '*')
        ) {
            return false;
        }

        return true;

    }

    /**
     * @param string $entity
     */
    public function setRootEntity ($entity) {

        $this->rootEntity = $entity;

    }

}