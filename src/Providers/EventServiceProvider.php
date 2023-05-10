<?php
namespace SOSTheBlack\Repository\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 * @package SOSTheBlack\Repository\Providers
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'SOSTheBlack\Repository\Events\RepositoryEntityCreated' => [
            'SOSTheBlack\Repository\Listeners\CleanCacheRepository'
        ],
        'SOSTheBlack\Repository\Events\RepositoryEntityUpdated' => [
            'SOSTheBlack\Repository\Listeners\CleanCacheRepository'
        ],
        'SOSTheBlack\Repository\Events\RepositoryEntityDeleted' => [
            'SOSTheBlack\Repository\Listeners\CleanCacheRepository'
        ]
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        $events = app('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
