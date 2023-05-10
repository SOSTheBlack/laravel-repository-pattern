<?php

namespace SOSTheBlack\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use SOSTheBlack\Repository\Generators\Commands\BindingsCommand;
use SOSTheBlack\Repository\Generators\Commands\ControllerCommand;
use SOSTheBlack\Repository\Generators\Commands\CriteriaCommand;
use SOSTheBlack\Repository\Generators\Commands\EntityCommand;
use SOSTheBlack\Repository\Generators\Commands\PresenterCommand;
use SOSTheBlack\Repository\Generators\Commands\RepositoryCommand;
use SOSTheBlack\Repository\Generators\Commands\TransformerCommand;
use SOSTheBlack\Repository\Generators\Commands\ValidatorCommand;

/**
 * Class RepositoryServiceProvider
 * @package SOSTheBlack\Repository\Providers
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;


    /**
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/repository.php' => config_path('repository.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/repository.php', 'repository');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'repository');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands(RepositoryCommand::class);
        $this->commands(TransformerCommand::class);
        $this->commands(PresenterCommand::class);
        $this->commands(EntityCommand::class);
        $this->commands(ValidatorCommand::class);
        $this->commands(ControllerCommand::class);
        $this->commands(BindingsCommand::class);
        $this->commands(CriteriaCommand::class);
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
