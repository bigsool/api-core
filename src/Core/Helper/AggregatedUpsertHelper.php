<?php


namespace Core\Helper;


use Core\Context\AggregatedModuleEntityUpsertContext;
use Core\Context\ApplicationContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModelAspect;
use Core\Registry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class AggregatedUpsertHelper {

    /**
     * @param AggregatedModuleEntityUpsertContext $context
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public static function setRelationshipsFromMetadata (AggregatedModuleEntityUpsertContext $context) {

        $modelNameForKeyPath = [];

        foreach ($context->getDefinition()->getModelAspects() as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();
            $modelNameForKeyPath[$relativeField] = $modelAspect->getModel();

            $pos = strrpos($relativeField, '.');

            if ($pos === false) {
                $modelName = $context->getDefinition()->getDBEntityName();
                $lastKeyPath = $relativeField;
            }
            else {
                if (!isset($modelNameForKeyPath[substr($relativeField, 0, $pos)])) {
                    throw new \RuntimeException('model name not defined for this prefix');
                }
                $modelName = $modelNameForKeyPath[substr($relativeField, 0, $pos)];
                $lastKeyPath = substr($relativeField, $pos + 1);
            }

            $mainEntityClassName = Registry::realModelClassName($modelName);
            $metadata = ApplicationContext::getInstance()->getClassMetadata($mainEntityClassName);
            $mapping = $metadata->getAssociationMapping($lastKeyPath);

            $explodedKeyPath = explode('.', $relativeField);
            if (count($explodedKeyPath) == 1) {
                $sourceKeyPath = 'main';
            }
            else {
                array_pop($explodedKeyPath);
                $sourceKeyPath = implode('.', $explodedKeyPath);;
            }
            $targetKeyPath = $relativeField;

            static::setRelationshipsFromAssociationMapping($context, $sourceKeyPath, $targetKeyPath, $mapping);

        }

    }

    /**
     * @param AggregatedModuleEntityUpsertContext $context
     * @param string                              $sourceRelativeField
     * @param string                              $targetRelativeField
     * @param array                               $mapping
     */
    protected static function setRelationshipsFromAssociationMapping ($context, $sourceRelativeField,
                                                                      $targetRelativeField,
                                                                      array $mapping) {

        $field1 = $mapping['fieldName'];
        $field2 = isset($mapping['mappedBy']) ? $mapping['mappedBy'] : $mapping['inversedBy'];

        $prefix1 = 'set';
        $prefix2 = 'set';

        if ($mapping['type'] == ClassMetadataInfo::ONE_TO_MANY || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field1 = substr($field1, 0, strlen($field1) - 1);
            $prefix1 = "add";
        }

        if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field2 = $mapping['inversedBy'];
            $field2 = substr($field2, 0, strlen($field2) - 1);
            $prefix2 = "add";
        }

        $fn = $prefix1 . ucfirst($field1);
        $sourceModel = static::getEntityForRelativeField($context, $sourceRelativeField);
        $targetModel = static::getEntityForRelativeField($context, $targetRelativeField);
        $sourceModel->$fn($targetModel);

        $fn = $prefix2 . ucfirst($field2);
        $targetModel->$fn($sourceModel);

    }

    /**
     * @param AggregatedModuleEntityUpsertContext $context
     * @param string                              $relativeField
     *
     * @return mixed|null
     */
    protected static function getEntityForRelativeField (AggregatedModuleEntityUpsertContext $context, $relativeField) {

        foreach ($context->getChildrenUpsertContextsWithModelAspect() as $childContextWithModuleEntity) {
            /**
             * @var ModuleEntityUpsertContext $childContext
             */
            $childContext = $childContextWithModuleEntity[0];

            /**
             * @var ModelAspect $modelAspect
             */
            $modelAspect = $childContextWithModuleEntity[1];

            if ($modelAspect->getRelativeField() == $relativeField) {
                return $childContext->getEntity();
            }
        }

        throw new \RuntimeException(sprintf('entity not found for relative field %s', $relativeField));

    }

}