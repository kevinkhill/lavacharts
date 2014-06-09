<?php namespace Khill\Lavacharts;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LavachartsServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot()
    {
        $this->package('khill/lavacharts');

        include __DIR__.'/../../routes.php';
    }

    public function register()
    {
        $this->app['lavacharts'] = $this->app->share(function($app)
        {
            return new Lavacharts();
        });

        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('Lava', 'Khill\Lavacharts\Facades\Lavacharts');
        });
    }

    public function provides()
    {
        return array('lavacharts');
    }

}
