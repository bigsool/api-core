<?php

use Core\Error\Error;
use Core\Error\LocalizedError;

$applicationContext = \Core\Context\ApplicationContext::getInstance();
$errorManager = $applicationContext->getErrorManager();
$t = $applicationContext->getTranslator();

@define('ERROR_INTERNAL_ERROR', -1);
$errorManager->defineError(new Error(ERROR_INTERNAL_ERROR, 'internal error'));

@define('ERROR_REQUEST_INVALID', -2);
$errorManager->defineError(new Error(ERROR_REQUEST_INVALID, 'invalid request'));

@define('ERROR_SERVICE_NOT_FOUND', -3);
$errorManager->defineError(new Error(ERROR_SERVICE_NOT_FOUND, 'service not found', ERROR_REQUEST_INVALID));

@define('ERROR_METHOD_NOT_FOUND', -4);
$errorManager->defineError(new Error(ERROR_METHOD_NOT_FOUND, 'method not found', ERROR_REQUEST_INVALID));

@define('ERROR_CLIENT_IS_INVALID', -5);
$errorManager->defineError(new Error(ERROR_CLIENT_IS_INVALID, 'invalid client', ERROR_REQUEST_INVALID));

@define('ERROR_PROTOCOL_IS_INVALID', -6);
$errorManager->defineError(new Error(ERROR_PROTOCOL_IS_INVALID, 'invalid protocol', ERROR_REQUEST_INVALID));

@define('ERROR_PERMISSION_DENIED', 7);
$errorManager->defineError(new LocalizedError(ERROR_PERMISSION_DENIED,
                                              $t->trans('ERROR_PERMISSION_DENIED', [], NULL, 'fr'),
                                              $t->trans('ERROR_PERMISSION_DENIED', [], NULL, 'en')));

@define('ERROR_BAD_VERSION', -8);
$errorManager->defineError(new Error(ERROR_BAD_VERSION,
                                     $t->trans('ERROR_BAD_VERSION', [], NULL, 'fr'),
                                     $t->trans('ERROR_BAD_VERSION', [], NULL, 'en')));

@define('ERROR_API_UNAVAILABLE', -9);
$errorManager->defineError(new Error(ERROR_API_UNAVAILABLE,
                                     $t->trans('ERROR_API_UNAVAILABLE', [], NULL, 'fr'),
                                     $t->trans('ERROR_API_UNAVAILABLE', [], NULL, 'en'),
                                     ERROR_INTERNAL_ERROR));


@define('ERROR_BAD_ENTITY', 16028);
$errorManager->defineError(new Error(ERROR_BAD_ENTITY, 'requested entity not available', ERROR_REQUEST_INVALID));

@define('ERROR_BAD_FIELD', 16029);
$errorManager->defineError(new Error(ERROR_BAD_FIELD, 'one or more field are invalid', ERROR_REQUEST_INVALID));

@define('ERROR_INVALID_PARAM', 16100);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM, 'one or more parameters are invalid'));
@define('ERROR_INVALID_PARAM_STRING', 16101);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_STRING, 'string parameter expected', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_EMAIL', 16102);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_EMAIL, 'invalid email address', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_INT', 16103);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_INT, 'invalid integer', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_CHOICE', 16104);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_CHOICE, 'value not in white list', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_DATETIME', 16105);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_DATETIME, 'invalid datetime', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_OBJECT', 16106);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_OBJECT, 'invalid object', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_NULL', 16107);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NULL, 'null expected', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_LENGTH', 16108);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_LENGTH, 'out of range value', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_NOT_NULL', 16109);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NOT_NULL, 'empty parameter', ERROR_INVALID_PARAM));
@define('ERROR_MISSING_PARAM', 16110);
$errorManager->defineError(new Error(ERROR_MISSING_PARAM, 'missing parameter', ERROR_INVALID_PARAM));

@define('ERROR_COMPANY_NOT_FOUND', 16201);
$errorManager->defineError(new Error(ERROR_COMPANY_NOT_FOUND, 'company not found'));

@define('ERROR_USER_NOT_FOUND', 16301);
$errorManager->defineError(new Error(ERROR_USER_NOT_FOUND, 'user not found'));
@define('ERROR_CREDENTIAL_ALREADY_EXIST', 16302);
$errorManager->defineError(new LocalizedError(ERROR_CREDENTIAL_ALREADY_EXIST,
                                              $t->trans('ERROR_CREDENTIAL_ALREADY_EXIST', [], NULL, 'fr'),
                                              $t->trans('ERROR_CREDENTIAL_ALREADY_EXIST', [], NULL, 'en')));
// "ce login est déjà utilisé", 'this login is already used'
@define('ERROR_ACCOUNT_ALREADY_CONFIRMED', 16304);
$errorManager->defineError(new Error(ERROR_ACCOUNT_ALREADY_CONFIRMED,
                                     $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'fr'),
                                     $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'en')));

@define('ERROR_IPAD_ALREADY_CONNECTED', 16305);
$errorManager->defineError(new Error(ERROR_IPAD_ALREADY_CONNECTED,
                                     $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'fr'),
                                     $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'en')));
//"votre compte est déjà connecté sur un autre iPad",'Your account is already connected on another iPad'
@define('ERROR_PRODUCT_NOT_FOUND', 16306);
$errorManager->defineError(new Error(ERROR_PRODUCT_NOT_FOUND, 'product not found'));
@define('ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE', 16307);
$errorManager->defineError(new Error(ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE, 'cannot login with old plan without archipad license'));
