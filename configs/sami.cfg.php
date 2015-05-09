<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Laravel')
    ->exclude('Traits')
    ->in(__DIR__.'/../src')
;

$versions = GitVersionCollection::create(__DIR__)
    ->addFromTags('2.5.1')
    ->add('traits', 'v3.0 branch');

return new Sami($iterator, array(
    'theme'                => 'default',
    'versions'             => $versions,
    'title'                => 'Lavacharts API',
    'build_dir'            => __DIR__.'/../build/%version%',
    'cache_dir'            => __DIR__.'/../cache/%version%',
    'default_opened_level' => 2,
));
