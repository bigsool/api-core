<?php

require_once __DIR__.'/vendor/autoload.php';

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = __DIR__.'/src')
;

return new Sami($iterator, array(
    'build_dir'            => __DIR__.'/doc/build',
    'cache_dir'            => __DIR__.'/doc/cache',
    'default_opened_level' => 1,
));
