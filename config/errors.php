<?php

use Core\Error\Error;

$errorManager = \Core\Context\ApplicationContext::getInstance()->getErrorManager();

@define('ERR_INTERNAL_ERROR', -1);
$errorManager->defineError(new Error(ERR_INTERNAL_ERROR, 'erreur interne', 'internal error'));

@define('ERR_REQUEST_INVALID', -2);
$errorManager->defineError(new Error(ERR_REQUEST_INVALID, 'requête invalide', 'invalid request'));

@define('ERR_SERVICE_NOT_FOUND', -3);
$errorManager->defineError(new Error(ERR_SERVICE_NOT_FOUND, 'service introuvable', 'service not found',
                                     ERR_REQUEST_INVALID));

@define('ERR_METHOD_NOT_FOUND', -4);
$errorManager->defineError(new Error(ERR_METHOD_NOT_FOUND, 'méthode introuvable', 'method not found',
                                     ERR_REQUEST_INVALID));

@define('ERR_CLIENT_IS_INVALID', -5);
$errorManager->defineError(new Error(ERR_CLIENT_IS_INVALID, 'client invalide', 'invalid client',
                                     ERR_REQUEST_INVALID));

@define('ERR_PROTOCOL_IS_INVALID', -6);
$errorManager->defineError(new Error(ERR_PROTOCOL_IS_INVALID, 'protocole non reconnu', 'invalid protocol',
                                     ERR_REQUEST_INVALID));

@define('ERR_PERMISSION_DENIED', -7);
$errorManager->defineError(new Error(ERR_PERMISSION_DENIED, 'accès refusé', 'permission denied'));

@define('ERR_BAD_VERSION', -8);
$errorManager->defineError(new Error(ERR_BAD_VERSION, 'version du client obsolète', 'out of date client version'));

@define('ERR_API_UNAVAILABLE', -9);
$errorManager->defineError(new Error(ERR_API_UNAVAILABLE, 'service temporairement indisponible',
                                     'service temporarily unavailable', ERR_INTERNAL_ERROR));

@define('ERROR_INVALID_PARAM', -100);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM, "au moins un paramètre est invalid",
                                     'one or more parameters are invalid'));
@define('ERROR_INVALID_PARAM_STRING', -101);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_STRING, "paramètre de type chaine de caractères attendu",
                                     'string parameter expected'));
@define('ERROR_INVALID_PARAM_EMAIL', -102);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_EMAIL, "email invalide",
                                     'invalid email'));
@define('ERROR_INVALID_PARAM_INT', -103);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_INT, "int invalide",
                                     'invalid int'));
@define('ERROR_INVALID_PARAM_CHOICE', -104);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_CHOICE, "valeur non acceptée",
                                     'value not in white list'));
@define('ERROR_INVALID_PARAM_DATETIME', -105);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_DATETIME, "datetime invalide",
                                     'invalid datetime'));
@define('ERROR_INVALID_PARAM_OBJECT', -106);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_OBJECT, "object invalide",
                                     'invalid object'));
@define('ERROR_INVALID_PARAM_NULL', -107);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NULL, "null attendu",
                                     'null expected'));