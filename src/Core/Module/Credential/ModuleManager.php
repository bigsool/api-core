<?php


namespace Core\Module\Credential;

use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Field\Field;
use Core\Field\RelativeField;
use Core\Filter\StringFilter;
use Core\Module\AddressBook\Validation;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Rule\FieldRule;
use Symfony\Component\HttpFoundation\Cookie;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function loadActions (ApplicationContext &$appCtx) {

        $self = $this;

        $appCtx->addAction(new SimpleAction('Core\Credential', 'login', NULL, ['login'    => [new Validation()],
                                                                                'password' => [new Validation()]
        ],
            function (ActionContext $context) {

                $appCtx = ApplicationContext::getInstance();

                $params = $context->getVerifiedParams();
                $helper = new Helper;
                $authToken = $helper->login($context, $params);

                $appCtx->getOnSuccessActionQueue()->addAction($appCtx->getAction('Core\Credential', 'setAuthCookie'),
                                                              ['authToken' => $authToken]);

                $credential = $helper->getCredentialFromLogin($params['login']);

                return [
                    'authToken' => $authToken,
                    'login'     => $params['login'],
                    'id'        => $credential->getId(),
                ];

            }));

        $appCtx->addAction(new SimpleAction('Core\Credential', 'setAuthCookie', [],
                                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                $response = $ctx->getRequestContext()->getResponse();

                if (is_null($response)) {

                    throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                }

                $appCtx = ApplicationContext::getInstance();
                $expire = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

                $response->headers->setCookie(new Cookie('authToken', json_encode($ctx->getParam('authToken')),
                                                         $expire,'/',null,false,false));


            }));

        $appCtx->addAction(new SimpleAction('Core\Credential', 'checkAuth', [],
                                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                $authToken = $ctx->getParam('authToken');

                $helper = new Helper();
                $credential = $helper->checkAuthToken($authToken);

                return $credential;

            }));

        $appCtx->addAction(new SimpleAction('Core\Credential', 'renewAuthCookie', [],
                                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                $response = $ctx->getRequestContext()->getResponse();

                if (is_null($response)) {

                    throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                }

                $authToken = $ctx->getParam('authToken');
                $credentialId = $ctx->getParam('credentialId');

                $helper = new Helper();
                $newAuthToken = $helper->renewAuthToken($authToken, $credentialId);

                $appCtx = ApplicationContext::getInstance();
                $expire = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

                $response->headers->setCookie(new Cookie('authToken', json_encode($newAuthToken),
                                                         $expire,'/',null,false,false));

            }));

        /**
         * @param ApplicationContext $appCtx
         */
        $appCtx->addAction(new SimpleAction('Core\Credential', 'create', NULL, ['login'    => [new Validation()],
                                                                                 'type'     => [new Validation()],
                                                                                 'password' => [new Validation()]
        ],
            function (ActionContext $context) {

                $appCtx = ApplicationContext::getInstance();

                $params = $context->getVerifiedParams();
                $helper = new Helper;

                $filter = $appCtx->getFilterByName('CredentialForLogin');

                $internalReqCtx = RequestContext::createNewInternalRequestContext();
                $ctx = $appCtx->getActionContext($internalReqCtx, 'Core\Credential', 'create');

                $helper->findCredential($ctx, [new RelativeField('*')], [$filter], ['login' => $params['login']]);

                if (count($ctx['credentials']) != 0) {

                    throw $appCtx->getErrorManager()->getFormattedError(ERROR_CREDENTIAL_ALREADY_EXIST);

                }

                $helper->createCredential($ctx, $params);

                return $ctx['credential'];

            }));


        $appCtx->addAction(new SimpleAction('Core\Credential', 'update', NULL, [
            'id'              => [new Validation(), true],
            'login'           => [new Validation()],
            'password'        => [new Validation()],
            'currentPassword' => [new Validation(), true]
        ], function (ActionContext $context) {

            $appCtx = ApplicationContext::getInstance();

            $params = $context->getVerifiedParams();

            $password = $context->getAuth()->getCredential()->getPassword();

            $helper = new Helper;

            $credential = $helper->getCredentialFromId($params['id']);

            unset($params['id']);

            if (!password_verify($params['currentPassword'], $password)) {
                throw $appCtx->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);
            }

            unset($params['currentPassword']);

            $helper->updateCredential($context, $credential, $params);

            $credential = $context['credential'];

            return $credential;

        }));

        $appCtx->addAction(new SimpleAction('Core\Credential', 'logout', NULL, [],
            function (ActionContext $context) {

                $response = $context->getRequestContext()->getResponse();
                $response->headers->clearCookie('authToken');

            }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $context->addFilter(new StringFilter('Credential', 'CredentialForLogin', 'login = :login'));
        $context->addFilter(new StringFilter('Credential', 'CredentialForId', 'id = :id'));

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

        $context->addRule(new FieldRule(new Field('Credential', 'password'),
                                        new StringFilter('Credential', 'passwordIsForbidden', '1 = 0')));

    }

}