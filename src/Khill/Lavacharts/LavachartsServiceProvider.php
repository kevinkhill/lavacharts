<?php namespace Khill\Lavacharts;

use Illuminate\Support\ServiceProvider;

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
            return new Lavacharts($app['view'], $app['config']);
        });

        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
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
