<?php


namespace Core\Module\Client;

use Archipad\Model\Client;
use Archipad\Model\Device;
use Core\Action\Action;
use Core\Action\GenericAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
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

            new GenericAction('Core\Client', 'create', [], [], function (ActionContext $context) {

                /**
                 * @var Client $client
                 */
                $client = $this->getModuleEntity('Client')->create([], $context);

                if ($context->doesParamExist('UUID')) {

                    /**
                     * @var Device $device
                     */
                    $device =
                        $context->newDerivedContextFor('Core\Client', 'createOrUpdateDevice')
                                ->process($context->getParams());
                    $client->setDevice($device);
                    $device->addClient($client);

                }

                $this->getModuleEntity('Client')->save($client);

                return $client;

            }),
            new GenericAction('Core\Client', 'createOrUpdateDevice', [], ['UUID' => [new DeviceDefinition()]],
                function (ActionContext $context) {

                    $deviceQryCtx =
                        new FindQueryContext('Device', $context->getRequestContext()->copyWithoutRequestedFields());
                    $deviceQryCtx->addField('*');
                    $deviceQryCtx->addFilter('DeviceForUUID', $context->getParam('UUID'));
                    /**
                     * @var Device $device
                     */
                    $device = $deviceQryCtx->findOne(false);

                    if ($device) {
                        $this->getModuleEntity('Device')->update($device->getId(), $context->getParams(), $context);
                    }
                    else {
                        $device = $this->getModuleEntity('Device')->create($context->getParams(), $context);
                    }
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