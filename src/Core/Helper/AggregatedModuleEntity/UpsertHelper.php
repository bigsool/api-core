<?php


namespace Core\Helper\AggregatedModuleEntity;


use Core\Context\AggregatedModuleEntityUpsertContext;
use Core\Context\ApplicationContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModelAspect;
use Core\Registry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class UpsertHelper {

    /**
     * @param AggregatedModuleEntityUpsertContext $context
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public static function setRelationshipsFromMetadata (AggregatedModuleEntityUpsertContext $context) {

        $modelNameForKeyPath = [];

        foreach ($context->getEnabledAspects() as $modelAspect) {

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
                $sourceKeyPath = NULL;
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
            $field1 = self::singularNameFromPlurialName($field1);
            $prefix1 = "add";
        }

        if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field2 = isset($mapping['inversedBy']) ? $mapping['inversedBy'] : $mapping['mappedBy'];
            $field2 = self::singularNameFromPlurialName($field2);
            $prefix2 = "add";
        }

        $fn = $prefix1 . ucfirst($field1);
        $sourceModel = static::getEntityForRelativeField($context, $sourceRelativeField);
        $targetModel = static::getEntityForRelativeField($context, $targetRelativeField);
        $sourceModel->$fn($targetModel);

        $fn = $prefix2 . ucfirst($field2);
        $targetModel->$fn($sourceModel);

    }

    private static function singularNameFromPlurialName ($name) {

        $nameSize = strlen($name);

        if (substr($name,$nameSize - 3,$nameSize) == 'ies') { // handle dependencies => dependency
            return substr($name, 0, $nameSize - 3)."y";
        }

        return substr($name, 0, strlen($name) - 1);

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