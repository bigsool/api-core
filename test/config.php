<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

if (!file_exists($file = __DIR__ . '/../vendor/autoload.php')
    && !file_exists($file = __DIR__ . '/../../../../vendor/autoload.php')
) {
    throw new RuntimeException('autoload file autoload.php not found');
}

require_once $file;

$config = Setup::createYAMLMetadataConfiguration([__DIR__ . "/yml/"], true, __DIR__ . '/proxy/', new ArrayCache());
$config->addCustomHydrationMode('RestrictedObjectHydrator', 'Core\Doctrine\Hydrator\RestrictedObjectHydrator');
$config->addCustomHydrationMode('ArrayIdHydrator', 'Core\Doctrine\Hydrator\ArrayIdHydrator');
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/test.api.core.db.sqlite',
);
$entityManager = EntityManager::create($conn, $config);

// this query activate the foreign key in sqlite
$entityManager->getConnection()->query('PRAGMA foreign_keys = ON');