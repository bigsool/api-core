<?php

use Core\Error\Error;
use Core\Error\LocalizedError;

$applicationContext = \Core\Context\ApplicationContext::getInstance();
$errorManager = $applicationContext->getErrorManager();
$t = $applicationContext->getTranslator();

defined('ERROR_INTERNAL_ERROR') || define('ERROR_INTERNAL_ERROR', 1);
$errorManager->defineError(new Error(ERROR_INTERNAL_ERROR, 'internal error'));

defined('ERROR_REQUEST_INVALID') || define('ERROR_REQUEST_INVALID', 2);
$errorManager->defineError(new Error(ERROR_REQUEST_INVALID, 'invalid request'));

defined('ERROR_SERVICE_NOT_FOUND') || define('ERROR_SERVICE_NOT_FOUND', 3);
$errorManager->defineError(new Error(ERROR_SERVICE_NOT_FOUND, 'service not found', ERROR_REQUEST_INVALID));

defined('ERROR_METHOD_NOT_FOUND') || define('ERROR_METHOD_NOT_FOUND', 4);
$errorManager->defineError(new Error(ERROR_METHOD_NOT_FOUND, 'method not found', ERROR_REQUEST_INVALID));

defined('ERROR_CLIENT_IS_INVALID') || define('ERROR_CLIENT_IS_INVALID', 5);
$errorManager->defineError(new Error(ERROR_CLIENT_IS_INVALID, 'invalid client', ERROR_REQUEST_INVALID));

defined('ERROR_PROTOCOL_IS_INVALID') || define('ERROR_PROTOCOL_IS_INVALID', 6);
$errorManager->defineError(new Error(ERROR_PROTOCOL_IS_INVALID, 'invalid protocol', ERROR_REQUEST_INVALID));

defined('ERROR_PERMISSION_DENIED') || define('ERROR_PERMISSION_DENIED', 7);
$errorManager->defineError(new LocalizedError(ERROR_PERMISSION_DENIED,
                                              $t->trans('ERROR_PERMISSION_DENIED', [], NULL, 'fr'),
                                              $t->trans('ERROR_PERMISSION_DENIED', [], NULL, 'en')));

defined('ERROR_BAD_VERSION') || define('ERROR_BAD_VERSION', 8);
$errorManager->defineError(new LocalizedError(ERROR_BAD_VERSION,
                                              $t->trans('ERROR_BAD_VERSION', [], NULL, 'fr'),
                                              $t->trans('ERROR_BAD_VERSION', [], NULL, 'en')));

defined('ERROR_API_UNAVAILABLE') || define('ERROR_API_UNAVAILABLE', 9);
$errorManager->defineError(new LocalizedError(ERROR_API_UNAVAILABLE,
                                              $t->trans('ERROR_API_UNAVAILABLE', [], NULL, 'fr'),
                                              $t->trans('ERROR_API_UNAVAILABLE', [], NULL, 'en'),
                                              ERROR_INTERNAL_ERROR));


defined('ERROR_AUTH_TOKEN_EXPIRED') || define('ERROR_AUTH_TOKEN_EXPIRED', 19);
$errorManager->defineError(new Error(ERROR_AUTH_TOKEN_EXPIRED, 'Auth token expired', ERROR_PERMISSION_DENIED));


defined('ERROR_BAD_ENTITY') || define('ERROR_BAD_ENTITY', 16028);
$errorManager->defineError(new Error(ERROR_BAD_ENTITY, 'requested entity not available', ERROR_REQUEST_INVALID));

defined('ERROR_BAD_FIELD') || define('ERROR_BAD_FIELD', 16029);
$errorManager->defineError(new Error(ERROR_BAD_FIELD, 'one or more field are invalid', ERROR_REQUEST_INVALID));

defined('ERROR_INVALID_PARAM') || define('ERROR_INVALID_PARAM', 16100);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM, 'one or more parameters are invalid'));
defined('ERROR_INVALID_PARAM_STRING') || define('ERROR_INVALID_PARAM_STRING', 16101);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_STRING, 'string parameter expected', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_EMAIL') || define('ERROR_INVALID_PARAM_EMAIL', 16102);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_EMAIL, 'invalid email address', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_INT') || define('ERROR_INVALID_PARAM_INT', 16103);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_INT, 'invalid integer', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_CHOICE') || define('ERROR_INVALID_PARAM_CHOICE', 16104);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_CHOICE, 'value not in white list', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_DATETIME') || define('ERROR_INVALID_PARAM_DATETIME', 16105);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_DATETIME, 'invalid datetime', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_OBJECT') || define('ERROR_INVALID_PARAM_OBJECT', 16106);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_OBJECT, 'invalid object', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_NULL') || define('ERROR_INVALID_PARAM_NULL', 16107);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NULL, 'null expected', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_LENGTH') || define('ERROR_INVALID_PARAM_LENGTH', 16108);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_LENGTH, 'out of range value', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_NOT_NULL') || define('ERROR_INVALID_PARAM_NOT_NULL', 16109);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NOT_NULL, 'empty parameter', ERROR_INVALID_PARAM));
defined('ERROR_MISSING_PARAM') || define('ERROR_MISSING_PARAM', 16110);
$errorManager->defineError(new Error(ERROR_MISSING_PARAM, 'missing parameter', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_FLOAT') || define('ERROR_INVALID_PARAM_FLOAT', 16111);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_FLOAT, 'invalid float', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_HEXA') || define('ERROR_INVALID_PARAM_HEXA', 16112);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_HEXA, 'invalid hexa', ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_REGEX') || define('ERROR_INVALID_PARAM_REGEX', 16113);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_REGEX, 'value not match with expected pattern',
                                     ERROR_INVALID_PARAM));
defined('ERROR_INVALID_PARAM_BUCKET') || define('ERROR_INVALID_PARAM_BUCKET', 16114);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_BUCKET, 'invalid bucket name',
                                     ERROR_INVALID_PARAM));
defined('ERROR_INVALID_SHARED_REPORT_ID') || define('ERROR_INVALID_SHARED_REPORT_ID', 16115);
$errorManager->defineError(new Error(ERROR_INVALID_SHARED_REPORT_ID, 'invalid report id',
    ERROR_INVALID_PARAM));

defined('ERROR_COMPANY_NOT_FOUND') || define('ERROR_COMPANY_NOT_FOUND', 16201);
$errorManager->defineError(new Error(ERROR_COMPANY_NOT_FOUND, 'company not found'));

defined('ERROR_USER_NOT_FOUND') || define('ERROR_USER_NOT_FOUND', 16301);
$errorManager->defineError(new LocalizedError(ERROR_USER_NOT_FOUND,
                                              $t->trans('ERROR_USER_NOT_FOUND', [], NULL, 'fr'),
                                              $t->trans('ERROR_USER_NOT_FOUND', [], NULL, 'en')));

defined('ERROR_CREDENTIAL_ALREADY_EXIST') || define('ERROR_CREDENTIAL_ALREADY_EXIST', 16302);
$errorManager->defineError(new LocalizedError(ERROR_CREDENTIAL_ALREADY_EXIST,
                                              $t->trans('ERROR_CREDENTIAL_ALREADY_EXIST', [], NULL, 'fr'),
                                              $t->trans('ERROR_CREDENTIAL_ALREADY_EXIST', [], NULL, 'en')));
// "ce login est déjà utilisé", 'this login is already used'
defined('ERROR_ACCOUNT_ALREADY_CONFIRMED') || define('ERROR_ACCOUNT_ALREADY_CONFIRMED', 16304);
$errorManager->defineError(new LocalizedError(ERROR_ACCOUNT_ALREADY_CONFIRMED,
                                              $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'fr'),
                                              $t->trans('ERROR_ACCOUNT_ALREADY_CONFIRMED', [], NULL, 'en')));

defined('ERROR_IPAD_ALREADY_CONNECTED') || define('ERROR_IPAD_ALREADY_CONNECTED', 16305);
$errorManager->defineError(new LocalizedError(ERROR_IPAD_ALREADY_CONNECTED,
                                              $t->trans('ERROR_IPAD_ALREADY_CONNECTED', [], NULL, 'fr'),
                                              $t->trans('ERROR_IPAD_ALREADY_CONNECTED', [], NULL, 'en')));

defined('ERROR_PRODUCT_NOT_FOUND') || define('ERROR_PRODUCT_NOT_FOUND', 16306);
$errorManager->defineError(new Error(ERROR_PRODUCT_NOT_FOUND, 'product not found'));

defined('ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE') || define('ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE', 16307);
$errorManager->defineError(new LocalizedError(ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE,
                                              $t->trans('ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE', [], NULL, 'fr'),
                                              $t->trans('ERROR_LOGIN_WITH_OLD_PLAN_WITHOUT_LICENSE', [], NULL, 'en')));

defined('ERROR_PROJECT_NOT_FOUND') || define('ERROR_PROJECT_NOT_FOUND', 16308);
$errorManager->defineError(new Error(ERROR_PROJECT_NOT_FOUND, 'project not found'));

defined('ERROR_PROJECT_PARTICIPANT_NOT_FOUND') || define('ERROR_PROJECT_PARTICIPANT_NOT_FOUND', 16309);
$errorManager->defineError(new Error(ERROR_PROJECT_PARTICIPANT_NOT_FOUND, 'project participation not found'));

defined('ERROR_HAVE_THIS_RECURRING_PRODUCT') || define('ERROR_HAVE_THIS_RECURRING_PRODUCT', 16310);
$errorManager->defineError(new LocalizedError(ERROR_HAVE_THIS_RECURRING_PRODUCT,
                                              $t->trans('ERROR_HAVE_THIS_RECURRING_PRODUCT', [], NULL, 'fr'),
                                              $t->trans('ERROR_HAVE_THIS_RECURRING_PRODUCT', [], NULL, 'en')));

defined('ERROR_WITH_STRIPE') || define('ERROR_WITH_STRIPE', 16311);
$errorManager->defineError(new LocalizedError(ERROR_WITH_STRIPE,
                                              $t->trans('ERROR_WITH_STRIPE', [], NULL, 'fr'),
                                              $t->trans('ERROR_WITH_STRIPE', [], NULL, 'en')));

defined('ERROR_CONNECTED_ELSEWHERE') || define('ERROR_CONNECTED_ELSEWHERE', 16312);
$errorManager->defineError(new LocalizedError(ERROR_CONNECTED_ELSEWHERE,
                                              $t->trans('ERROR_CONNECTED_ELSEWHERE', [], NULL, 'fr'),
                                              $t->trans('ERROR_CONNECTED_ELSEWHERE', [], NULL, 'en'),
                                              ERROR_PERMISSION_DENIED));

defined('ERROR_EMAIL_MUST_BE_CONFIRMED') || define('ERROR_EMAIL_MUST_BE_CONFIRMED', 16313);
$errorManager->defineError(new LocalizedError(ERROR_EMAIL_MUST_BE_CONFIRMED,
                                              $t->trans('ERROR_EMAIL_MUST_BE_CONFIRMED', [], NULL, 'fr'),
                                              $t->trans('ERROR_EMAIL_MUST_BE_CONFIRMED', [], NULL, 'en')));


defined('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAVE_PREMIUM') || define('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAVE_PREMIUM', 16314);
$errorManager->defineError(new LocalizedError(ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAVE_PREMIUM,
                                              $t->trans('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAVE_PREMIUM', [], NULL, 'fr'),
                                              $t->trans('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAVE_PREMIUM', [], NULL, 'en')));


defined('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAD_OFFER') || define('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAD_OFFER', 16315);
$errorManager->defineError(new LocalizedError(ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAD_OFFER,
                                              $t->trans('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAD_OFFER', [], NULL, 'fr'),
                                              $t->trans('ERROR_CANNOT_ACTIVATE_TRIAL_ALREADY_HAD_OFFER', [], NULL, 'en')));

defined('ERROR_SHARED_REPORT_NOT_FOUND') || define('ERROR_SHARED_REPORT_NOT_FOUND', 1002);
$errorManager->defineError(new LocalizedError(ERROR_SHARED_REPORT_NOT_FOUND,
                                              $t->trans('ERROR_SHARED_REPORT_NOT_FOUND', [], NULL, 'fr'),
                                              $t->trans('ERROR_SHARED_REPORT_NOT_FOUND', [], NULL, 'en')));

defined('ERROR_PLEASE_UPGRADE_ARCHIPAD') || define('ERROR_PLEASE_UPGRADE_ARCHIPAD', 16317);
$errorManager->defineError(new LocalizedError(ERROR_PLEASE_UPGRADE_ARCHIPAD,
                                              $t->trans('ERROR_PLEASE_UPGRADE_ARCHIPAD', [], NULL, 'fr'),
                                              $t->trans('ERROR_PLEASE_UPGRADE_ARCHIPAD', [], NULL, 'en')));

defined('ERROR_SHARED_REPORT_INVALID_PASSWORD') || define('ERROR_SHARED_REPORT_INVALID_PASSWORD', 16318);
$errorManager->defineError(new LocalizedError(ERROR_SHARED_REPORT_INVALID_PASSWORD,
                                              $t->trans('ERROR_SHARED_REPORT_INVALID_PASSWORD', [], NULL, 'fr'),
                                              $t->trans('ERROR_SHARED_REPORT_INVALID_PASSWORD', [], NULL, 'en')));

defined('ERROR_PROJECT_TEMPLATE_NOT_FOUND') || define('ERROR_PROJECT_TEMPLATE_NOT_FOUND', 16319);
$errorManager->defineError(new LocalizedError(ERROR_PROJECT_TEMPLATE_NOT_FOUND,
                                              $t->trans('ERROR_PROJECT_TEMPLATE_NOT_FOUND', [], NULL, 'fr'),
                                              $t->trans('ERROR_PROJECT_TEMPLATE_NOT_FOUND', [], NULL, 'en')));

defined('ERROR_PROJECT_TEMPLATE_ALREADY_EXISTS') || define('ERROR_PROJECT_TEMPLATE_ALREADY_EXISTS', 16320);
$errorManager->defineError(new Error(ERROR_PROJECT_TEMPLATE_ALREADY_EXISTS, 'project template already exists'));