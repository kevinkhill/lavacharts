<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('callbacks')
    ->exclude('Facades')
	->exclude('Traits')
    ->in(__DIR__.'/src/Khill/Lavacharts/')
;

return new Sami($iterator, array(
    //'theme'                => 'symfony',
    'title'                => 'Lavacharts API',
    'build_dir'            => __DIR__.'/build',
    'cache_dir'            => __DIR__.'/cache',
    'default_opened_level' => 2,
));