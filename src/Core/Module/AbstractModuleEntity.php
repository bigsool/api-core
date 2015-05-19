<?php


namespace Core\Module;


use Core\Context\ApplicationContext;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Registry;

abstract class AbstractModuleEntity implements ModuleEntity {

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var CalculatedField[]
     */
    protected $fields;

    /**
     * @var Filter[]
     */
    protected $filters;

    /**
     * @var string
     */
    protected $applicationContext;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param ApplicationContext $applicationContext
     * @param string             $entityName
     * @param Filter[]           $filters
     * @param CalculatedField[]  $fields
     */
    public function __construct (ApplicationContext $applicationContext, $entityName, array $filters = [],
                                 array $fields = []) {

        $this->entityName = $entityName;
        $this->fields = $fields;
        $this->filters = $filters;
        $this->applicationContext = $applicationContext;

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return $this->entityName;
    }

    /**
     * @return CalculatedField[]
     */
    public function getCalculatedFieldCallbacks () {

        return $this->fields;

    }

    /**
     * @param mixed $fields
     */
    public function setFields ($fields) {

        $this->fields = $fields;
    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param mixed $filters
     */
    public function setFilters ($filters) {

        $this->filters = $filters;

    }

    /**
     * @param Registry $registry
     */
    public function setRegistry (Registry $registry) {

        $this->registry = $registry;

    }

}