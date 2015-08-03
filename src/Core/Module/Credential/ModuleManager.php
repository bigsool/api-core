<?php


namespace Core\Module\Credential;

use Core\Action\Action;
use Core\Action\GenericAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Error\ToResolveException;
use Core\Field\Field;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Rule\FieldRule;
use Core\Rule\Rule;
use Symfony\Component\HttpFoundation\Cookie;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        return [
            new GenericAction('Core\Credential', 'login', NULL, ['login'    => [new CredentialDefinition()],
                                                                 'password' => [new CredentialDefinition()],
                                                                 'authType' => [new CredentialDefinition()],
            ],
                function (ActionContext $context) use ($appCtx) {

                    $params = $context->getVerifiedParams();

                    $credentialHelper = $this->getCredentialHelper();
                    $credentials = $credentialHelper::credentialsForAuthParams($params);

                    // TODO : we may add superUser in LoginHistory
                    $loginHistory =
                        $this->getModuleEntity('LoginHistory')->create(['credential' => $credentials[0]], $context);
                    $this->getModuleEntity('LoginHistory')->save($loginHistory);

                    $additionalParams = $context->getVerifiedParam('additionalParams', []);

                    $authenticationHelper = $this->getAuthenticationHelper();
                    $authToken = $authenticationHelper::generateAuthToken($credentials, NULL, NULL, $additionalParams);

                    $appCtx->getOnSuccessActionQueue()->addAction($appCtx->getAction('Core\Credential',
                                                                                     'setAuthCookie'),
                                                                  ['authToken' => $authToken]);

                    $context->getRequestContext()->setAuthToken($authToken);
                    $context->getRequestContext()->getAuth()->setCredential($credentials[0]);
                    if (count($credentials) == 2) {
                        $context->getRequestContext()->getAuth()->setSuperUserCredential($credentials[1]);
                    }

                    $credential = $credentials[0];

                    return [
                        'authToken' => $authToken,
                        'login'     => $credential->getLogin(),
                        'id'        => $credential->getId(),
                    ];

                }),
            new GenericAction('Core\Credential', 'setAuthCookie', [],
                              ['authToken' => [new AuthenticationValidation()]], function (ActionContext $ctx) {

                    $response = $ctx->getRequestContext()->getResponse();

                    if (is_null($response)) {

                        throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                    }

                    $appCtx = $ctx->getApplicationContext();
                    $expire = time() + $appCtx->getConfigManager()->getConfig()['credential']['expirationAuthToken'];

                    $response->headers->setCookie(new Cookie('authToken', json_encode($ctx->getParam('authToken')),
                                                             $expire, '/', NULL, false, false));

                }),
            new GenericAction('Core\Credential', 'checkAuth', [],
                              ['authToken' => [new AuthenticationValidation()]], function (ActionContext $ctx) {

                    $authToken = $ctx->getParam('authToken');

                    $authenticationHelper = $this->getAuthenticationHelper();
                    $credentials = $authenticationHelper::checkAuthToken($authToken);

                    return $credentials;

                }),
            new GenericAction('Core\Credential', 'renewAuthCookie', [],
                              ['authToken' => [new AuthenticationValidation()]], function (ActionContext $ctx) {

                    $response = $ctx->getRequestContext()->getResponse();

                    if (is_null($response)) {

                        throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                    }

                    $authToken = $ctx->getParam('authToken');
                    $credentials = $ctx->getParam('credentials');

                    $authenticationHelper = $this->getAuthenticationHelper();
                    $newAuthToken = $authenticationHelper::renewAuthToken($authToken, $credentials);

                    $appCtx = $ctx->getApplicationContext();
                    $expire = time() + $appCtx->getConfigManager()->getConfig()['credential']['expirationAuthToken'];

                    $response->headers->setCookie(new Cookie('authToken', json_encode($newAuthToken),
                                                             $expire, '/', NULL, false, false));

                }),
            new GenericAction('Core\Credential', 'create', NULL, ['login'    => [new CredentialDefinition()],
                                                                  'type'     => [new CredentialDefinition()],
                                                                  'password' => [new CredentialDefinition()]
            ],
                function (ActionContext $context) {

                    $params = $context->getVerifiedParams();

                    $internalReqCtx = RequestContext::createNewInternalRequestContext();

                    $findQueryContext = new FindQueryContext('Credential', $internalReqCtx);
                    $findQueryContext->addField('*');
                    $findQueryContext->addFilter('CredentialForLogin', $params['login']);

                    // TODO count request directly
                    if (count($findQueryContext->findAll()) != 0) {
                        throw new ToResolveException(ERROR_CREDENTIAL_ALREADY_EXIST);
                    }

                    $credential = $this->getModuleEntity('Credential')->create($context->getParams(), $context);

                    $this->getModuleEntity('Credential')->save($credential);

                    return $credential;

                }),
            new GenericAction('Core\Credential', 'update', NULL, [
                'id'              => [new CredentialDefinition()],
                'login'           => [new CredentialDefinition(), true],
                'password'        => [new CredentialDefinition(), true],
                'currentPassword' => [new CredentialDefinition(), true]
            ], function (ActionContext $context) {

                $params = $context->getVerifiedParams();

                $password = $context->getAuth()->getCredential()->getPassword();

                if (!password_verify($params['currentPassword'], $password)) {
                    throw new ToResolveException(ERROR_PERMISSION_DENIED);
                }

                $context->unsetParam('currentPassword');

                $credential =
                    $this->getModuleEntity('Credential')
                         ->update($context->getParam('id'), $context->getParams(), $context);

                $this->getModuleEntity('Credential')->save($credential);

                return $credential;

            }),
            new GenericAction('Core\Credential', 'logout', NULL, [],
                function (ActionContext $context) {

                    $response = $context->getRequestContext()->getResponse();
                    $response->headers->clearCookie('authToken');

                })
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return Filter[]
     */
    public function createModuleFilters (ApplicationContext &$context) {

        return [
            new StringFilter('Credential', 'MyCredential', 'login = :__LOGIN__')
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return Rule[]
     */
    public function createRules (ApplicationContext &$context) {

        return [
            new FieldRule(new Field('Credential', 'password'),
                          new StringFilter('Credential', 'passwordIsForbidden', '1 = 0'))
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'Credential',
            'LoginHistory'
        ];

    }

    /**
     * @return CredentialHelper
     */
    protected function getCredentialHelper () {

        return ApplicationContext::getInstance()->getHelperClassName('Credential');

    }

    /**
     * @return AuthenticationHelper
     */
    protected function getAuthenticationHelper () {

        return ApplicationContext::getInstance()->getHelperClassName('Authentication');

    }

}