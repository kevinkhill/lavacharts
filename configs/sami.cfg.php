<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Laravel')
    //->exclude('Traits')
    ->in($dir = __DIR__.'/../src')
;

$versions = GitVersionCollection::create($dir)
    ->addFromTags('2.5.2')
    ->add('dashboards', 'v3.0 branch');

return new Sami($iterator, [
    'theme'                => 'default',
    //'versions'             => $versions,
    'title'                => 'Lavacharts API',
    'build_dir'            => __DIR__.'/../build/%version%',
    'cache_dir'            => __DIR__.'/../cache/%version%',
    'remote_repository'    => new GitHubRemoteRepository('khill/lavacharts', dirname($dir)),
    'default_opened_level' => 2,
]);
