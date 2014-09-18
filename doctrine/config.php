<?php

require_once __DIR__ . "/../vendor/autoload.php";

$config =
    \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/../doctrine/model/yml"), true,
                                                               __DIR__ . '/../src/');
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => sys_get_temp_dir() . '/archiweb-proto.db.sqlite',
);
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

// this query activate the foreign key in sqlite
$entityManager->getConnection()->query('PRAGMA foreign_keys = ON');