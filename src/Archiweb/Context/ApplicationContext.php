<?php


namespace Archiweb\Context;


use Archiweb\Field\Field;
use Archiweb\Filter\Filter;
use Archiweb\Registry;
use Archiweb\Rule\Rule;
use Archiweb\RuleProcessor;
use Doctrine\ORM\EntityManager;

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

        return new Registry($this->entityManager,$this);

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

} 