<?php


namespace Core\Module\Credential;

use Core\Action\SimpleAction;
use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Field\Field;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Module\AddressBook\Validation;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Rule\FieldRule;
use Symfony\Component\HttpFoundation\Cookie;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $self = $this;

        $context->addAction(new SimpleAction('Core\Credential', 'login', NULL, ['login'    => [new Validation()],
                                                                                'password' => [new Validation()]
        ],
            function (ActionContext $context) {

                $appCtx = ApplicationContext::getInstance();

                $params = $context->getVerifiedParams();
                $helper = new Helper;
                $authToken = $helper->login($context, $params);

                $appCtx->getOnSuccessActionQueue()->enqueue($appCtx->getAction('Core\Credential', 'setAuthCookie'),
                                                            ['authToken' => $authToken]);

                return ['success' => true,
                        'data'    => [
                            'authToken' => $authToken,
                            'email'     => $params['login']
                        ]
                ];

            }));

        $context->addAction(new SimpleAction('Core\Credential', 'setAuthCookie', [],
                                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                $response = $ctx->getRequestContext()->getResponse();

                if (is_null($response)) {

                    throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                }

                $response->headers->setCookie(new Cookie('authToken', json_encode($ctx->getParam('authToken'))));

            }));

        $context->addAction(new SimpleAction('Core\Credential', 'checkAuth', [],
                                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                $appCtx = ApplicationContext::getInstance();
                $authToken = $ctx->getParam('authToken');

                // TODO THIERRY: real authentication //
                $login = $authToken[1];
                $helper = new Helper;
                $filter = $appCtx->getFilterByEntityAndName('Credential', 'filterByLogin');
                $ctx = new ActionContext(new RequestContext());
                $helper->findCredential($ctx, false, [new KeyPath('*')], [$filter], ['login' => $login],
                                        [Auth::INTERNAL]);
                $credential = $ctx['credentials'][0];
                //////////////////


                return $credential;

            }));

        /**
         * @param ApplicationContext $context
         */
        $context->addAction(new SimpleAction('Core\Credential', 'create', NULL, ['login'    => [new Validation()],
                                                                                 'type'     => [new Validation()],
                                                                                 'password' => [new Validation()]
        ],
            function (ActionContext $context) {

                $appCtx = ApplicationContext::getInstance();

                $params = $context->getVerifiedParams();
                $helper = new Helper;

                $filter = $appCtx->getFilterByEntityAndName('Credential', 'filterByLogin');

                $ctx = new ActionContext(new RequestContext());

                $helper->findCredential($ctx, true, [new KeyPath('*')], [$filter], ['login' => $params['login']],
                                        [Auth::INTERNAL]);

                if (count($ctx['credentials']) != 0) {

                    throw $appCtx->getErrorManager()->getFormattedError(ERROR_CREDENTIAL_ALREADY_EXIST);

                }

                $helper->createCredential($ctx, $params);

                return $ctx['credential'];

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

        $context->addRule(new FieldRule(new Field('Credential', 'password'),
                                        new StringFilter('Credential', 'passwordIsForbidden', '1 = 0')));

    }

}