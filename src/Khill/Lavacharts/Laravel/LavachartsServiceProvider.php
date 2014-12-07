<?php namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LavachartsServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->package('khill/lavacharts');

        include __DIR__.'/BladeTemplateExtensions.php';
    }

    public function register()
    {
        $this->app['lavacharts'] = $this->app->share(
            function ($app) {
                return new \Khill\Lavacharts\Lavacharts;
            }
        );

        $this->app->booting(
            function () {
                $loader = AliasLoader::getInstance();
                $loader->alias('Lava', 'Khill\Lavacharts\Laravel\LavachartsFacade');
            }
        );
    }

    public function provides()
    {
        return array('lavacharts');
    }
}
