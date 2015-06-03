<?php

namespace Core\Module;

use Core\Action\SimpleAction;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Util\ModelConverter;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * Load model aspects of each AggregatedModuleEntity
     * This must be done once every ModuleEntities are loaded
     * @param ApplicationContext $context
     */
    public function loadModelAspects(ApplicationContext $context) {

        // loading of model aspect must be done after the definition of all Module Entities
        foreach ($this->moduleEntities as $moduleEntity) {
            $moduleEntityDefinition = $moduleEntity->getDefinition();
            if ($moduleEntityDefinition instanceof AggregatedModuleEntityDefinition) {
                $moduleEntityDefinition->loadModelAspects($context);
            }
        }

    }

}