<?php


namespace Core\Module\Marketing;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function createActions (ApplicationContext &$appCtx) {

        $appCtx->addAction(new BasicCreateAction('Core\Marketing', 'MarketingInfo', [], [
            'knowsFrom' => [new Validation()]
        ]));

        $appCtx->addAction(new BasicUpdateAction('Core\Marketing', 'MarketingInfo', [], [
            'knowsFrom' => [new Validation(), true]
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function createModuleFilters (ApplicationContext &$context) {
        // TODO: Implement loadFilters() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'MarketingHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function createRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

}