<?php


namespace Core\Module;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Filter\StringFilter;

class DbModuleEntity extends AbstractModuleEntity {

    /**
     * @var string
     */
    protected $helperClassName;

    /**
     * @param ApplicationContext     $applicationContext
     * @param ModuleEntityDefinition $definition
     */
    public function __construct (ApplicationContext $applicationContext, ModuleEntityDefinition $definition) {

        parent::__construct($applicationContext, $definition);

    }

    /**
     * @param ModuleEntityUpsertContext $upsertContext
     *
     * @return mixed|null
     */
    protected function upsert (ModuleEntityUpsertContext $upsertContext) {

        $entity = NULL;

        if ($upsertContext->getEntityId()) {
            $entityName = $this->getDefinition()->getEntityName();
            $qryCtx =
                new FindQueryContext($entityName, $upsertContext->getActionContext()->getRequestContext()
                                                                ->copyWithoutRequestedFields());
            $qryCtx->addField('*');
            $qryCtx->addFilter(new StringFilter($entityName, '', 'id = :id'), $upsertContext->getEntityId());

            $entity = $qryCtx->findOne();
        }
        else {
            $entity = $this->createEntity();
        }

        if (!is_object($entity)) {
            throw new \RuntimeException('$entity must be an object');
        }

        // TODO : should we loop on setter or on params ? validatedParams or AllParamas ?
        //$acceptedFields = array_keys($this->getDefinition()->getConstraintsList());
        // This doesn't work because passwordV1 in credential is defined in V1Compatibility.
        // CredentialDefinition is defined in Credential and doesn't know passwordV1
        foreach ($upsertContext->getValidatedParams() as $field => $param) {
            if ($field == 'id' && !is_callable([$entity, 'setId'], false)/*|| !in_array($field, $acceptedFields)*/) {
                continue;
            }
            $method = 'set' . ucfirst($field);
            if (!is_callable([$entity, $method], false, $callableName)) {
                throw new \RuntimeException($callableName . ' is not callable');
            }
            $entity->$method($param);
        }

        return $entity;

    }

}