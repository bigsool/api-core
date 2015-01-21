<?php

if (!file_exists($file = __DIR__ . '/../vendor/autoload.php')
    && !file_exists($file = __DIR__ . '/../../../../vendor/autoload.php')
) {
    throw new RuntimeException('autoload file autoload.php not found');
}

require_once $file;

$config =
    \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/../model/"), true,
                                                               __DIR__ . '/../proxy/');
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/archiweb-proto.db.sqlite',
);
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

// this query activate the foreign key in sqlite
$entityManager->getConnection()->query('PRAGMA foreign_keys = ON');