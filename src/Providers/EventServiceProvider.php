<?php

namespace SOSTheBlack\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use SOSTheBlack\Repository\Events\RepositoryEntityCreated;
use SOSTheBlack\Repository\Events\RepositoryEntityDeleted;
use SOSTheBlack\Repository\Events\RepositoryEntityUpdated;
use SOSTheBlack\Repository\Listeners\CleanCacheRepository;

/**
 * Class EventServiceProvider
 * @package SOSTheBlack\Repository\Providers
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RepositoryEntityCreated::class => [
            CleanCacheRepository::class
        ],
        RepositoryEntityUpdated::class => [
            CleanCacheRepository::class
        ],
        RepositoryEntityDeleted::class => [
            CleanCacheRepository::class
        ]
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot(): void
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
    public function register(): void
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens(): array
    {
        return $this->listen;
    }
}
