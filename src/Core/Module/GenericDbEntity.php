<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Helper\GenericHelper;

class GenericDbEntity extends AbstractModuleEntity {

    /**
     * @var Callable
     */
    protected $preCreateCallable;

    /**
     * @var Callable
     */
    protected $createCallable;

    /**
     * @var Callable
     */
    protected $postCreateCallable;

    /**
     * @var Callable
     */
    protected $preSaveCallable;

    /**
     * @var Callable
     */
    protected $saveCallable;

    /**
     * @var Callable
     */
    protected $postSaveCallable;

    /**
     * @var Callable
     */
    protected $preDeleteCallable;

    /**
     * @var Callable
     */
    protected $deleteCallable;

    /**
     * @var Callable
     */
    protected $postDeleteCallable;

    /**
     * @var Callable
     */
    protected $preFindCallable;

    /**
     * @var Callable
     */
    protected $findCallable;

    /**
     * @var Callable
     */
    protected $postFindCallable;

    /**
     * @var \Core\Helper\GenericHelper
     */
    protected $helper;

    /**
     * @param ApplicationContext $applicationContext
     * @param string             $entityName
     * @param Filter[]           $filters
     * @param CalculatedField[]  $fields
     */
    public function __construct (ApplicationContext $applicationContext, $entityName, array $filters = [],
                                 array $fields = []) {

        parent::__construct($applicationContext, $entityName, $filters, $fields);

        $this->createCallable = function (ActionContext $actionContext, $params) {

            $entity = $this->getHelper()->create($actionContext, $params);

            return $entity;

        };

        $this->findCallable = function (FindQueryContext $findQueryContext) {

            return $this->registry->find($findQueryContext);

        };

        $this->saveCallable = function ($entity) use ($applicationContext) {

            $this->registry->save($entity);

        };

        $this->deleteCallable = function ($entity) use ($applicationContext) {

            $this->registry->delete($entity);

        };

        $this->setHelper(new GenericHelper($applicationContext, $entityName));

    }

    /**
     * @return \Core\Helper\GenericHelper
     */
    public function getHelper () {

        return $this->helper;

    }

    /**
     * @param \Core\Helper\GenericHelper $helper
     */
    public function setHelper (GenericHelper $helper) {

        $this->helper = $helper;

    }

    /**
     * @param Callable $preDeleteCallable
     */
    public function setPreDeleteCallable (callable $preDeleteCallable) {

        $this->preDeleteCallable = $preDeleteCallable;
    }

    /**
     * @param Callable $deleteCallable
     */
    public function setDeleteCallable (callable $deleteCallable) {

        $this->deleteCallable = $deleteCallable;
    }

    /**
     * @param Callable $postDeleteCallable
     */
    public function setPostDeleteCallable (callable $postDeleteCallable) {

        $this->postDeleteCallable = $postDeleteCallable;
    }

    /**
     * @param Callable $preSaveCallable
     */
    public function setPreSaveCallable ($preSaveCallable) {

        $this->preSaveCallable = $preSaveCallable;
    }

    /**
     * @param Callable $saveCallable
     */
    public function setSaveCallable ($saveCallable) {

        $this->saveCallable = $saveCallable;
    }

    /**
     * @param Callable $postSaveCallable
     */
    public function setPostSaveCallable ($postSaveCallable) {

        $this->postSaveCallable = $postSaveCallable;
    }

    /**
     * @param Callable $preFindCallable
     */
    public function setPreFindCallable ($preFindCallable) {

        $this->preFindCallable = $preFindCallable;
    }

    /**
     * @param Callable $findCallable
     */
    public function setFindCallable ($findCallable) {

        $this->findCallable = $findCallable;
    }

    /**
     * @param Callable $postFindCallable
     */
    public function setPostFindCallable ($postFindCallable) {

        $this->postFindCallable = $postFindCallable;
    }

    /**
     * @param Callable $preCreateCallable
     */
    public function setPreCreateCallable ($preCreateCallable) {

        $this->preCreateCallable = $preCreateCallable;
    }

    /**
     * @param Callable $createCallable
     */
    public function setCreateCallable ($createCallable) {

        $this->createCallable = $createCallable;
    }

    /**
     * @param Callable $postCreateCallable
     */
    public function setPostCreateCallable ($postCreateCallable) {

        $this->postCreateCallable = $postCreateCallable;
    }

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     * @return mixed
     */
    public function create (ActionContext $actionContext, array $params) {

        if ($this->preCreateCallable) {
            call_user_func($this->preCreateCallable, $actionContext, $params);
        }

        $entityObj = call_user_func($this->createCallable, $actionContext, $params);

        if ($this->postCreateCallable) {
            call_user_func($this->postCreateCallable, $actionContext, $entityObj, $params);
        }

        return $entityObj;

    }

    /**
     * @param mixed $entity
     */
    public function delete ($entity) {

        if ($this->preDeleteCallable) {
            call_user_func($this->preDeleteCallable, $entity);
        }

        $realModelClassName = $this->registry->realModelClassName($this->entityName);
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        call_user_func($this->deleteCallable, $entity);

        if ($this->postDeleteCallable) {
            call_user_func($this->postDeleteCallable, $entity);
        }

    }

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext) {

        if ($this->preFindCallable) {
            call_user_func($this->preFindCallable, $findQueryContext);
        }

        $result = call_user_func($this->findCallable, $findQueryContext);

        if ($this->postFindCallable) {
            call_user_func($this->postFindCallable, $findQueryContext, $result);
        }

        return $result;

    }

    /**
     * @param mixed $entity
     */
    public function save ($entity) {

        if ($this->preSaveCallable) {
            call_user_func($this->preSaveCallable, $entity);
        }

        $realModelClassName = $this->registry->realModelClassName($this->entityName);
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        call_user_func($this->saveCallable, $entity);

        if ($this->postSaveCallable) {
            call_user_func($this->postSaveCallable, $entity);
        }

    }

}