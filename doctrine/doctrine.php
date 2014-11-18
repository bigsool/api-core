<?php

$helperSet = require __DIR__.'/config.php';

\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet, [
    new \Core\Doctrine\Command\BuildEntitiesCommand
]);
 