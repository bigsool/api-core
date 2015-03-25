<?php


namespace Core\Module\Credential;

use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Field\Field;
use Core\Filter\StringFilter;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Rule\FieldRule;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $self = $this;

        $context->addAction(new SimpleAction('Core\Credential', 'login', NULL, ['login'    => [new Validation()],
                                                                                'password' => [new Validation()]
        ],
            function (ActionContext $context) use ($self) {

                $params = $context->getVerifiedParams();
                $helper = new Helper($this, $params);
                $authToken = $helper->login($context, $params);

                return ['success' => true,
                        'data'    => [
                            'authToken' => $authToken,
                            'email'     => $params['login']
                        ]
                ];

            }));

        /**
         * @param ApplicationContext $context
         */
        $context->addAction(new SimpleAction('Core\Credential', 'create', NULL, ['login'    => [new Validation()],
                                                                                 'type'     => [new Validation()],
                                                                                 'password' => [new Validation()]
        ],
            function (ActionContext $context) use ($self) {

                $params = $context->getVerifiedParams();
                $helper = new Helper($this, $params);
                $helper->createCredential($context, $params);

                return $context['credential'];

            }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $context->addFilter(new StringFilter('Credential', 'filterByLogin', 'login = :login'));

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

        $context->addRule(new FieldRule(new Field('Credential', 'salt'),
                                        new StringFilter('Credential', 'saltIsForbidden', '1 = 0')));

    }

}