<?php
namespace SOSTheBlack\Repository\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package SOSTheBlack\Repository\Providers
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../resources/config/repository.php' => config_path('repository.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../../resources/config/repository.php', 'repository');

        $this->loadTranslationsFrom(__DIR__ . '/../../../resources/lang', 'repository');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('SOSTheBlack\Repository\Generators\Commands\RepositoryCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\TransformerCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\PresenterCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\EntityCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\ValidatorCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\ControllerCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\BindingsCommand');
        $this->commands('SOSTheBlack\Repository\Generators\Commands\CriteriaCommand');
        $this->app->register('SOSTheBlack\Repository\Providers\EventServiceProvider');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
