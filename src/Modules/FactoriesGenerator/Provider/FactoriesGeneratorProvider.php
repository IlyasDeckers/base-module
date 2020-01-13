<?php

namespace Clockwork\Base\Modules\FactoriesGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Clockwork\Base\FactoriesGenerator\Console\Commands\GenerateFactoriesCommand;
use Clockwork\Base\TransactionMiddleware;
use Clockwork\Base\ValidatorMiddleware;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'test-factory-helper');
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
