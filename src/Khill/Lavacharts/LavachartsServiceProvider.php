<?php namespace Khill\Lavacharts;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LavachartsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('khill/lavacharts');

        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('lavacharts');
    }

}
