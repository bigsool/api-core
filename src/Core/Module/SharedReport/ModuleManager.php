<?php


namespace Core\Module\SharedReport;


use Archiweb\Model\SharedReport;
use Core\Action\Action;
use Core\Action\GenericAction;
use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class ModuleManager extends \Core\Module\ModuleManager {

    /**
     * @param ApplicationContext $context
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$context) {

        return [
            new GenericAction('Core\ShareReport', 'share', Auth::AUTHENTICATED, [
                'reportId' => [new SharedReportDefinition()],
                'password' => [new SharedReportDefinition()],
            ], function (ActionContext $context) {

                /**
                 * @var SharedReport $sharedReport
                 */
                $sharedReport = $this->getModuleEntity('SharedReport')->create($context->getVerifiedParams(), $context);
                $this->getModuleEntity('SharedReport')->save($sharedReport);

                return $sharedReport;

            }),

        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return String[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'SharedReport',
        ];

    }

}