<?php

namespace Clockwork\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Clockwork\Base\Console\Commands\GenerateFactoriesCommand;
use Clockwork\Base\TransactionMiddleware;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('transaction', TransactionMiddleware::class);

        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'test-factory-helper');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('command.test-factory-helper.generate',
            function ($app) {
                return new GenerateFactoriesCommand($app['files'], $app['view']);
            }
        );
        $this->commands('command.test-factory-helper.generate');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.test-factory-helper.generate');
    }
}
