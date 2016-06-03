<?php


namespace Core\Expression;


use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Registry;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Parameter extends Value {

    /**
     * @var string
     */
    protected $realName;

    /**
     * @var mixed
     */
    protected $parameterValue;

    /**
     * @var bool
     */
    protected $isParameterGiven = false;

    /**
     * @param mixed $parameterValue
     */
    public function setParameterValue ($parameterValue) {

        $this->parameterValue = $parameterValue;
        $this->isParameterGiven = true;

    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!self::isValidParameter($value)) {
            throw new \RuntimeException('invalid parameter');
        }

        parent::__construct($value);

    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isValidParameter ($value) {

        if (!is_string($value) || !preg_match('/^:[a-zA-Z_0-9-]+$/', $value)) {
            return false;
        }

        return true;

    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        if (!($context instanceof FindQueryContext)) {
            throw new \RuntimeException('invalid context');
        }

        $name = substr($this->getValue(), 1);
        $value = $this->isParameterGiven ? $this->parameterValue : $context->getParam($name);

        if (!$this->isParameterGiven && !$context->parameterExists($name)) {
            throw new \RuntimeException("parameter $name not found");
        }

        $this->realName = $this->getValue() . '_' . rand();

        $registry->setParameter($this->realName, $value);

        return $this->realName;

    }

    public function getRealName () {

        if (!$this->realName) {
            throw new RuntimeException('resolve must be called before getRealName');
        }

        return $this->realName;

    }

} 