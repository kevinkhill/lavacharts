<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Facade')
    ->exclude('Traits')
    ->exclude('LavachartsServiceProvider.php')
    ->in(__DIR__.'/src/Khill/Lavacharts')
;

$versions = GitVersionCollection::create(__DIR__)
    ->addFromTags('v1.0.0')
    ->add('2.0', '2.0 branch')
    ->add('master', 'master branch')
;

return new Sami($iterator, array(
    'theme'                => 'enhanced',
    'versions'             => $versions,
    'title'                => 'Lavacharts API',
    'build_dir'            => __DIR__.'/build/%version%',
    'cache_dir'            => __DIR__.'/cache/%version%',
    'default_opened_level' => 2,
));
