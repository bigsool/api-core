<?php

use Archiweb\Error\ErrorManager;
use Archiweb\Error\Error;

define('ERR_CUSTOM_ERROR', 0);
ErrorManager::addDefinedError(new Error(ERR_CUSTOM_ERROR, '', ''));

define('ERR_INTERNAL_ERROR', 1);
ErrorManager::addDefinedError(new Error(ERR_INTERNAL_ERROR, 'erreur interne', 'internal error'));

define('ERR_REQUEST_INVALID', 2);
ErrorManager::addDefinedError(new Error(ERR_REQUEST_INVALID, 'requête invalide', 'invalid request'));

define('ERR_SERVICE_NOT_FOUND', 3);
ErrorManager::addDefinedError(new Error(ERR_SERVICE_NOT_FOUND, 'service introuvable', 'service not found', ERR_REQUEST_INVALID));

define('ERR_METHOD_NOT_FOUND', 4);
ErrorManager::addDefinedError(new Error(ERR_METHOD_NOT_FOUND, 'méthode introuvable', 'method not found', ERR_REQUEST_INVALID));

define('ERR_CLIENT_IS_INVALID', 5);
ErrorManager::addDefinedError(new Error(ERR_CLIENT_IS_INVALID, 'client invalide', 'invalid client', ERR_REQUEST_INVALID));

define('ERR_PROTOCOL_IS_INVALID', 6);
ErrorManager::addDefinedError(new Error(ERR_PROTOCOL_IS_INVALID, 'protocole non reconnu', 'invalid protocol', ERR_REQUEST_INVALID));

define('ERR_PERMISSION_DENIED', 7);
ErrorManager::addDefinedError(new Error(ERR_PERMISSION_DENIED, 'accès refusé', 'permission denied'));

define('ERR_BAD_VERSION', 8);
ErrorManager::addDefinedError(new Error(ERR_BAD_VERSION, 'version du client obsolète', 'out of date client version'));

define('ERR_API_UNAVAILABLE', 9);
ErrorManager::addDefinedError(new Error(ERR_API_UNAVAILABLE, 'service temporairement indisponible', 'service temporarily unavailable', ERR_INTERNAL_ERROR));

define('ERR_DB_UNKNOWN_ERROR', 10);
ErrorManager::addDefinedError(new Error(ERR_DB_UNKNOWN_ERROR, 'erreur inconnue lié à la base de données', 'unknown database error', ERR_INTERNAL_ERROR));

define('ERR_DB_CANNOT_BEGIN_TRANSACTION', 12);
ErrorManager::addDefinedError(new Error(ERR_DB_CANNOT_BEGIN_TRANSACTION, 'impossible de démarrer une transaction', 'database cannot begin transaction', ERR_INTERNAL_ERROR));

define('ERR_DB_CANNOT_COMMIT', 13);
ErrorManager::addDefinedError(new Error(ERR_DB_CANNOT_COMMIT, 'commit impossible', 'database cannot commit', ERR_INTERNAL_ERROR));

define('ERR_DB_CANNOT_ROLLBACK', 14);
ErrorManager::addDefinedError(new Error(ERR_DB_CANNOT_ROLLBACK, 'rollback impossible', 'database cannot rollback', ERR_INTERNAL_ERROR));

define('ERR_EMAIL_CANNOT_BE_SENT', 15);
ErrorManager::addDefinedError(new Error(ERR_EMAIL_CANNOT_BE_SENT, "erreur lors de l'envoi du mail", 'email cannot be sent', ERR_INTERNAL_ERROR));

define('ERR_UNKNOWN_AMAZON_ERROR', 16);
ErrorManager::addDefinedError(new Error(ERR_UNKNOWN_AMAZON_ERROR, "erreur amazon inconnue", 'unknown amazon error', ERR_INTERNAL_ERROR));

define('ERR_AUTH_TOKEN_EXPIRED', 19);
ErrorManager::addDefinedError(new Error(ERR_AUTH_TOKEN_EXPIRED, "jeton d'authentification expiré", 'auth token expired'));

define('ERR_PARAMS_INVALID', 100);
ErrorManager::addDefinedError(new Error(ERR_PARAMS_INVALID, "au moins un paramètre est invalid", 'one or more parameters are invalid'));

define('ERR_INVALID_PARAM_EMAIL', 101);
ErrorManager::addDefinedError(new Error(ERR_INVALID_PARAM_EMAIL, "adresse email invalide", 'email is invalid', ERR_PARAMS_INVALID));
