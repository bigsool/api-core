<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

if (!file_exists($file = __DIR__ . '/../vendor/autoload.php')
    && !file_exists($file = __DIR__ . '/../../../../vendor/autoload.php')
) {
    throw new RuntimeException('autoload file autoload.php not found');
}

require_once $file;

$config =
    Setup::createYAMLMetadataConfiguration([__DIR__ . "/../model/"], true, __DIR__ . '/../proxy/', new ArrayCache());
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/archiweb-proto.db.sqlite',
);
$entityManager = EntityManager::create($conn, $config);

// this query activate the foreign key in sqlite
$entityManager->getConnection()->query('PRAGMA foreign_keys = ON');