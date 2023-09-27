<?php

namespace SOSTheBlack\Repository\Listeners;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SOSTheBlack\Repository\Contracts\RepositoryInterface;
use SOSTheBlack\Repository\Events\RepositoryEventBase;
use SOSTheBlack\Repository\Helpers\CacheKeys;
use Throwable;

/**
 * Class CleanCacheRepository
 * @package SOSTheBlack\Repository\Listeners
 */
class CleanCacheRepository
{

    /**
     * @var CacheRepository|null
     */
    protected ?CacheRepository $cache = null;

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
     * @param RepositoryEventBase $event
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
