#!/usr/bin/env sh
rm -rf proxy Core/Model test.api.core.db.sqlite
php doctrine.php o:g:e .
php doctrine.php o:g:p proxy
php doctrine.php o:s:c
cp TestAccount.php Core/Model/