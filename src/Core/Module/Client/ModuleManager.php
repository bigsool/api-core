<?php


namespace Core\Module\Client;

use Core\Action\Action;
use Core\Action\GenericAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$context) {

        return [

            new GenericAction('Core\Client','create',[],[],function(ActionContext $context){

                $client = $this->getModuleEntity('Client')->create($context->getParams(), $context);
                $this->getModuleEntity('Client')->save($client);

                return $client;

            }),

            new GenericAction('Core\Client','createDevice',[],[],function(ActionContext $context){

                $device = $this->getModuleEntity('Device')->create($context->getParams(), $context);
                $this->getModuleEntity('Device')->save($device);

                return $device;

            }),

        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'Client',
            'Device',
        ];

    }

}