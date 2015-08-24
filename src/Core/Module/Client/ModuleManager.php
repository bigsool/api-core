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

                $device = NULL;

                if ($context->doesParamExist('UUID')) {
                    $params = $context->getParams();
                }
                else {
                    $params = ['UUID' => '', 'name' => '', 'type' => ''];
                }


                /**
                 * @var Device $device
                 */
                $device =
                    $context->newDerivedContextFor('Core\Client', 'createOrUpdateDevice')
                            ->process($params);


                $reqCtx = $context->getRequestContext();
                $params = [];
                $params['name'] = $reqCtx->getClientName();
                $params['version'] = $reqCtx->getClientVersion();
                $params['device'] = $device;

                $qryCtx = new FindQueryContext('Client', $reqCtx->createNewInternalRequestContext());
                $qryCtx->addFilter('ClientForDevice', $params['device']);
                $qryCtx->addFilter('ClientForName', $params['name']);
                $qryCtx->addFilter('ClientForVersion', $params['version']);

                /**
                 * @var Client $client
                 */
                $client = $qryCtx->findOne(false);

                if (!$client) {
                    $client = $this->getModuleEntity('Client')->create($params, $context);
                }

                if ($device) {
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