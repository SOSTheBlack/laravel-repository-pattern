<?php

namespace SOSTheBlack\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use SOSTheBlack\Repository\Generators\Commands\RepositoryCommand;

/**
 * Class LumenRepositoryServiceProvider
 * @package SOSTheBlack\Repository\Providers
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class LumenRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands(RepositoryCommand::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
