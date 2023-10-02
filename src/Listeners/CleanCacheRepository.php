<?php

namespace SOSTheBlack\Repository\Listeners;

use Throwable;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use SOSTheBlack\Repository\Helpers\CacheKeys;
use SOSTheBlack\Repository\Events\RepositoryEventBase;
use SOSTheBlack\Repository\Contracts\RepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Class CleanCacheRepository
 *
 * @package SOSTheBlack\Repository\Listeners
 */
class CleanCacheRepository
{

    /**
     * @var CacheRepository|null
     */
    protected CacheRepository|CacheManager|null $cache = null;

    /**
     * @var RepositoryInterface|null
     */
    protected ?RepositoryInterface $repository = null;

    /**
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * @var string|null
     */
    protected ?string $action = null;

    /**
     *
     */
    public function __construct()
    {
        $this->cache = app(config('repository.cache.repository', 'cache'));
    }

    /**
     * @param  RepositoryEventBase  $event
     */
    public function handle(RepositoryEventBase $event): void
    {
        try {
            $cleanEnabled = config("repository.cache.clean.enabled", true);

            if ($cleanEnabled) {
                $this->repository = $event->getRepository();
                $this->model = $event->getModel();
                $this->action = $event->getAction();

                if (config("repository.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));

                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
