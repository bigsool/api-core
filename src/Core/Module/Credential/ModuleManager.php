<?php


namespace Core\Module\Credential;

use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;
use Symfony\Component\Validator\Exception\RuntimeException;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $self = $this;

        $context->addAction(new SimpleAction('Core\Credential', 'login', NULL, ['login'  => [new Validation()],
                                                                                'password' => [new Validation()],
                                                                                'authToken' => [new Validation()]],
            function (ActionContext $context) use ($self) {

                $params = $context->getVerifiedParams();
                $helper = new Helper($this,$params);
                $authToken = $helper->login($context,$params);

                return ['success' => true,
                        'data' => [
                            'authToken' => $authToken,
                            'email' => $params['login']
                        ]];

        }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'CredentialHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}