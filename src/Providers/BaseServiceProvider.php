<?php

namespace Clockwork\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Clockwork\Base\Console\Commands\GenerateFactoriesCommand;
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
        $router = $this->app['router'];
        $router->aliasMiddleware('transaction', TransactionMiddleware::class);
        $router->aliasMiddleware('validator', ValidatorMiddleware::class);

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
