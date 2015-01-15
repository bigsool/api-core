<?php

namespace Core\Module\Account;


use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\MagicalModuleManager;
use Core\Validation\Constraints\Dictionary;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModuleManager extends MagicalModuleManager {

    public function load (ApplicationContext &$context) {

        $this->addAspect([
                             'model' => 'User',
                         ]);

        $this->addAspect([
                             'model'       => 'Company',
                             'prefix'      => 'company',
                             'keyPath'     => 'company',
                             'constraints' => [new Dictionary(), new NotBlank()],
                         ]);

        $this->addAspect([
                             'model'       => 'Storage',
                             'prefix'      => 'storage',
                             'keyPath'     => 'company.storage',
                             'constraints' => [new Blank()],
                         ]);

        parent::load($context);

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $self = $this;

        $context->addAction(new SimpleAction('Core\Account', 'create', NULL, [],
            function (ActionContext $context) use ($self) {

                $user = $self->magicalCreate($context);

                return $user;

            }));

        $context->addAction(new SimpleAction('Core\Account', 'update', NULL, [],
            function (ActionContext $context) use ($self) {

                $user = $self->magicalUpdate($context);

                return $user;

            }));

        $context->addAction(new SimpleAction('Core\Account', 'find', NULL, [],
            function (ActionContext $context) use ($self) {

                throw new \RuntimeException('Not implemented yet');

            }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {
        // TODO: Implement loadFields() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {
        // TODO: Implement loadFilters() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {
        // TODO: Implement loadHelpers() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRoutes (ApplicationContext &$context) {
        // TODO: Implement loadRoutes() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

}