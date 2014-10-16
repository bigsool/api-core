<?php


namespace Archiweb\Context;


use Archiweb\Action\Action;
use Archiweb\Error\ErrorManager;
use Archiweb\Field\Field;
use Archiweb\Filter\Filter;
use Archiweb\Registry;
use Archiweb\Rule\Rule;
use Archiweb\RuleProcessor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApplicationContext {

    /**
     * @var ApplicationContext
     */
    protected static $instance;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleProcessor
     */
    protected $ruleProcessor;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @var Rule[]
     */
    protected $rules = [];

    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var ErrorManager
     */
    protected $errorManager;

    /**
     * @var object[]
     */
    protected $helpers = [];

    /**
     *
     */
    protected function __construct () {

        $this->routes = new RouteCollection();

    }

    /**
     * @return ApplicationContext
     */
    public static function getInstance () {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    /**
     * @return RuleProcessor
     */
    public function getRuleProcessor () {

        return $this->ruleProcessor;

    }

    /**
     * @param RuleProcessor $ruleProcessor
     */
    public function setRuleProcessor (RuleProcessor $ruleProcessor) {

        $this->ruleProcessor = $ruleProcessor;

    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager (EntityManager $entityManager) {

        $this->entityManager = $entityManager;

    }

    /**
     * @param string $class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata ($class) {

        return $this->entityManager->getClassMetadata($class);

    }

    /**
     * @return Registry
     */
    public function getNewRegistry () {

        $registry = new Registry($this->entityManager, $this);

        $this->entityManager->getEventManager()->addEventSubscriber($registry);

        return $registry;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @param string $entity
     * @param string $name
     *
     * @return Filter
     */
    public function getFilterByEntityAndName ($entity, $name) {

        foreach ($this->getFilters() as $filter) {
            if ($filter->getEntity() == $entity && $filter->getName() == $name) {
                return $filter;
            }
        }

        throw new \RuntimeException('Filter not found');

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param \Archiweb\Field\Field $field
     */
    public function addField (Field $field) {

        $this->fields[] = $field;

    }

    /**
     * @param Rule $rule
     */
    public function addRule (Rule $rule) {

        $this->rules[] = $rule;

    }

    /**
     * @return Rule[]
     */
    public function getRules () {

        return $this->rules;

    }

    /**
     * @param string $entity
     *
     * @return \Archiweb\Field\Field[]
     */
    public function getFieldsByEntity ($entity) {

        $fields = [];
        foreach ($this->getFields() as $field) {
            if ($field->getEntity() == $entity) {
                $fields[] = $field;
            }
        }

        return $fields;

    }

    /**
     * @return \Archiweb\Field\Field[]
     */
    public function getFields () {

        return $this->fields;

    }

    /**
     * @param string $entity
     * @param string $name
     *
     * @return \Archiweb\Field\Field
     */
    public function getFieldByEntityAndName ($entity, $name) {

        foreach ($this->getFields() as $field) {
            if ($field->getEntity() == $entity && $field->getName() == $name) {
                return $field;
            }
        }

        throw new \RuntimeException('Field not found');

    }

    /**
     * @param Action $action
     */
    public function addAction (Action $action) {

        if (!in_array($action, $this->getActions(), true)) {
            $this->actions[] = $action;
        }

    }

    /**
     * @return Action[]
     */
    public function getActions () {

        return $this->actions;

    }

    /**
     * @param $module
     * @param $name
     *
     * @return Action
     */
    public function getAction ($module, $name) {

        foreach ($this->getActions() as $action) {
            if ($action->getModule() == $module && $action->getName() == $name) {
                return $action;
            }
        }

        throw new \RuntimeException('Action not found');

    }

    /**
     * @param string $name
     * @param object $helper
     */
    public function addHelper ($name, $helper) {

        if (!isset($this->helpers[$name])) {
            $this->helpers[$name] = $helper;
        }

    }

    /**
     * @return object[]
     */
    public function getHelpers () {

        return $this->helpers;

    }

    /**
     * @param string $name
     *
     * @return object
     */
    public function getHelper ($name) {

        if (isset($this->helpers[$name])) {
            return $this->helpers[$name];
        }

        throw new \RuntimeException('Helper not found');

    }

    /**
     * @param string $name
     * @param Route  $route
     */
    public function addRoute ($name, Route $route) {

        $this->routes->add($name, $route);

    }

    /**
     * @return RouteCollection
     */
    public function getRoutes () {

        return $this->routes;

    }

    /**
     * @return ErrorManager
     */
    public function getErrorManager () {

        if (!isset($this->errorManager)) {
            $errorManager = new \ReflectionClass('\Archiweb\Error\ErrorManager');
            $this->errorManager = $errorManager->newInstanceWithoutConstructor();
            $constructor = $errorManager->getConstructor();
            $constructor->setAccessible(true);
            $constructor->invoke($this->errorManager);
            $constructor->setAccessible(false);
        }

        return $this->errorManager;

    }

} 