<?php


namespace Archiweb\Context;


use Archiweb\Field;
use Archiweb\Filter\Filter;
use Archiweb\Registry;
use Archiweb\Rule\Rule;
use Archiweb\RuleManager;
use Doctrine\ORM\EntityManager;

class ApplicationContext {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleManager
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
     * @return RuleManager
     */
    public function getRuleManager () {

        return $this->ruleManager;

    }

    /**
     * @param RuleManager $ruleManager
     */
    public function setRuleManager (RuleManager $ruleManager) {

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

        return new Registry($this->entityManager);

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param Field $field
     */
    public function addField (Field $field) {

        $this->fields[] = $field;

    }

    /**
     * @return Field[]
     */
    public function getFields () {

        return $this->fields;

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
     * @return Field[]
     */
    public function getFieldsByEntity($entity) {
        // TODO: Implement getFieldsByEntity() method
    }

    /**
     * @param string $entity
     * @param string $name
     * @return Field
     */
    public function getFieldByEntityAndName($entity, $name) {
        // TODO: Implement getFieldByEntityAndName() method
    }

} 