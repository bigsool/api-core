<?php

use Core\Error\Error;

$errorManager = \Core\Context\ApplicationContext::getInstance()->getErrorManager();

@define('ERROR_INTERNAL_ERROR', -1);
$errorManager->defineError(new Error(ERROR_INTERNAL_ERROR, 'erreur interne', 'internal error'));

@define('ERROR_REQUEST_INVALID', -2);
$errorManager->defineError(new Error(ERROR_REQUEST_INVALID, 'requête invalide', 'invalid request'));

@define('ERROR_SERVICE_NOT_FOUND', -3);
$errorManager->defineError(new Error(ERROR_SERVICE_NOT_FOUND, 'service introuvable', 'service not found',
                                     ERROR_REQUEST_INVALID));

@define('ERROR_METHOD_NOT_FOUND', -4);
$errorManager->defineError(new Error(ERROR_METHOD_NOT_FOUND, 'méthode introuvable', 'method not found',
                                     ERROR_REQUEST_INVALID));

@define('ERROR_CLIENT_IS_INVALID', -5);
$errorManager->defineError(new Error(ERROR_CLIENT_IS_INVALID, 'client invalide', 'invalid client',
                                     ERROR_REQUEST_INVALID));

@define('ERROR_PROTOCOL_IS_INVALID', -6);
$errorManager->defineError(new Error(ERROR_PROTOCOL_IS_INVALID, 'protocole non reconnu', 'invalid protocol',
                                     ERROR_REQUEST_INVALID));

@define('ERROR_PERMISSION_DENIED', -7);
$errorManager->defineError(new Error(ERROR_PERMISSION_DENIED, 'accès refusé', 'permission denied'));

@define('ERROR_BAD_VERSION', -8);
$errorManager->defineError(new Error(ERROR_BAD_VERSION, 'version du client obsolète', 'out of date client version'));

@define('ERROR_API_UNAVAILABLE', -9);
$errorManager->defineError(new Error(ERROR_API_UNAVAILABLE, 'service temporairement indisponible',
                                     'service temporarily unavailable', ERROR_INTERNAL_ERROR));


@define('ERROR_BAD_ENTITY', -28);
$errorManager->defineError(new Error(ERROR_BAD_ENTITY, "entité demandé non disponible",
                                     'requested entity not available', ERROR_REQUEST_INVALID));

@define('ERROR_BAD_FIELD', -29);
$errorManager->defineError(new Error(ERROR_BAD_FIELD, "au moins un des fields est invalide",
                                     'one or more field are invalid', ERROR_REQUEST_INVALID));

@define('ERROR_INVALID_PARAM', -100);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM, "au moins un paramètre est invalid",
                                     'one or more parameters are invalid'));
@define('ERROR_INVALID_PARAM_STRING', -101);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_STRING, "paramètre de type chaine de caractères attendu",
                                     'string parameter expected', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_EMAIL', -102);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_EMAIL, "email invalide",
                                     'invalid email', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_INT', -103);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_INT, "int invalide",
                                     'invalid int', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_CHOICE', -104);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_CHOICE, "valeur non acceptée",
                                     'value not in white list', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_DATETIME', -105);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_DATETIME, "datetime invalide",
                                     'invalid datetime', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_OBJECT', -106);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_OBJECT, "object invalide",
                                     'invalid object', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_NULL', -107);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NULL, "null attendu",
                                     'null expected', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_LENGTH', -108);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_LENGTH, "valeur hors limites",
                                     'out of range value', ERROR_INVALID_PARAM));
@define('ERROR_INVALID_PARAM_NOT_NULL', -109);
$errorManager->defineError(new Error(ERROR_INVALID_PARAM_NOT_NULL, "paramètre vide",
                                     'empty parameter', ERROR_INVALID_PARAM));
@define('ERROR_MISSING_PARAM', -110);
$errorManager->defineError(new Error(ERROR_MISSING_PARAM, "paramètre manquant",
                                     'missing parameter', ERROR_INVALID_PARAM));

@define('ERROR_COMPANY_NOT_FOUND', -6001);
$errorManager->defineError(new Error(ERROR_COMPANY_NOT_FOUND, "société inexistante", 'company not found'));

@define('ERROR_USER_NOT_FOUND', -7001);
$errorManager->defineError(new Error(ERROR_USER_NOT_FOUND, "compte inexistant", 'user not found'));
@define('ERROR_CREDENTIAL_ALREADY_EXIST', -7002);
$errorManager->defineError(new Error(ERROR_CREDENTIAL_ALREADY_EXIST, "ce login est déjà utilisé",
                                     'this login is already used'));
@define('ERROR_ACCOUNT_ALREADY_CONFIRMED', -7004);
$errorManager->defineError(new Error(ERROR_ACCOUNT_ALREADY_CONFIRMED, "compte déjà vérifié",
                                     'account already confirmed'));

