<?php


namespace Core\Module\Credential;

use Core\Action\Action;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Error\ToResolveException;
use Core\Field\Field;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\AddressBook\Validation;
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
            new SimpleAction('Core\Credential', 'login', NULL, ['login'    => [new Validation()],
                                                                'password' => [new Validation()]
            ],
                function (ActionContext $context) {

                    $appCtx = ApplicationContext::getInstance();

                    $params = $context->getVerifiedParams();

                    $credential =
                        CredentialHelper::credentialForLoginAndPassword($params['login'], $params['password']);

                    $this->getModuleEntity('LoginHistory')->create($context, ['credential' => $credential]);

                    $authToken = AuthenticationHelper::generateAuthToken($credential);

                    $appCtx->getOnSuccessActionQueue()->addAction($appCtx->getAction('Core\Credential',
                                                                                     'setAuthCookie'),
                                                                  ['authToken' => $authToken]);

                    return [
                        'authToken' => $authToken,
                        'login'     => $params['login'],
                        'id'        => $credential->getId(),
                    ];

                }),
            new SimpleAction('Core\Credential', 'setAuthCookie', [],
                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                    $response = $ctx->getRequestContext()->getResponse();

                    if (is_null($response)) {

                        throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                    }

                    $appCtx = ApplicationContext::getInstance();
                    $expire = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

                    $response->headers->setCookie(new Cookie('authToken', json_encode($ctx->getParam('authToken')),
                                                             $expire, '/', NULL, false, false));

                }),
            new SimpleAction('Core\Credential', 'checkAuth', [],
                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                    $authToken = $ctx->getParam('authToken');

                    $credential = AuthenticationHelper::checkAuthToken($authToken);

                    return $credential;

                }),
            new SimpleAction('Core\Credential', 'renewAuthCookie', [],
                             ['authToken' => [new Validation()]], function (ActionContext $ctx) {

                    $response = $ctx->getRequestContext()->getResponse();

                    if (is_null($response)) {

                        throw new \RuntimeException('Calling setAuthCookie while the response is not set');

                    }

                    $authToken = $ctx->getParam('authToken');
                    $credentialId = $ctx->getParam('credentialId');

                    $credential = CredentialHelper::credentialForId($credentialId);
                    $newAuthToken = AuthenticationHelper::renewAuthToken($authToken, $credential);

                    $appCtx = ApplicationContext::getInstance();
                    $expire = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

                    $response->headers->setCookie(new Cookie('authToken', json_encode($newAuthToken),
                                                             $expire, '/', NULL, false, false));

                }),
            new SimpleAction('Core\Credential', 'create', NULL, ['login'    => [new Validation()],
                                                                 'type'     => [new Validation()],
                                                                 'password' => [new Validation()]
            ],
                function (ActionContext $context) {

                    $params = $context->getVerifiedParams();

                    $internalReqCtx = RequestContext::createNewInternalRequestContext();

                    $findQueryContext = new FindQueryContext('Credential', $internalReqCtx);
                    $findQueryContext->addField('*');
                    $findQueryContext->addFilter('CredentialForLogin', $params['login']);

                    // TODO request directly count
                    if (count($findQueryContext->findAll()) != 0) {
                        throw new ToResolveException(ERROR_CREDENTIAL_ALREADY_EXIST);
                    }

                    return $this->getModuleEntity('Credential')->create($context);

                }),
            new SimpleAction('Core\Credential', 'update', NULL, [
                'id'              => [new Validation(), true],
                'login'           => [new Validation()],
                'password'        => [new Validation()],
                'currentPassword' => [new Validation(), true]
            ], function (ActionContext $context) {

                $params = $context->getVerifiedParams();

                $password = $context->getAuth()->getCredential()->getPassword();

                if (!password_verify($params['currentPassword'], $password)) {
                    throw new ToResolveException(ERROR_PERMISSION_DENIED);
                }

                $context->unsetParam('currentPassword');

                return $this->getModuleEntity('Credential')->update($context);

            }),
            new SimpleAction('Core\Credential', 'logout', NULL, [],
                function (ActionContext $context) {

                    $response = $context->getRequestContext()->getResponse();
                    $response->headers->clearCookie('authToken');

                })
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new CredentialDefinition(),
            new LoginHistoryDefinition()
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

}