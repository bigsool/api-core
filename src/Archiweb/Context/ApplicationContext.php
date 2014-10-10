<?php


namespace Archiweb\Context;


use Archiweb\Action\Action;
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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleProcessor
     */
    protected $ruleManager;

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
     * @var \Archiweb\Action\Action[]
     */
    protected $actions = [];

    /**
     * @var RouteCollection
     */
    protected $routes;

    public function __construct () {

        $this->routes = new RouteCollection();

    }

    /**
     * @return RuleProcessor
     */
    public function getRuleManager () {

        return $this->ruleManager;

    }

    /**
     * @param RuleProcessor $ruleManager
     */
    public function setRuleManager (RuleProcessor $ruleManager) {

        $this->ruleManager = $ruleManager;

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

        return new Registry($this->entityManager, $this);

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

} 