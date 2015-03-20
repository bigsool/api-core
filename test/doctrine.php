<?php

$helperSet = require __DIR__ . '/cli-config.php';

\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet, [
]);
 