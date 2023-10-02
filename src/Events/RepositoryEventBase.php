<?php

namespace SOSTheBlack\Repository\Events;

use Illuminate\Database\Eloquent\Model;
use SOSTheBlack\Repository\Contracts\RepositoryEventBaseInterface;
use SOSTheBlack\Repository\Contracts\RepositoryInterface;

/**
 * Class RepositoryEventBase.
 */
abstract class RepositoryEventBase implements RepositoryEventBaseInterface
{
    /**
     * @var array|Model|null
     */
    protected Model|array|null $model;

    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * @var string
     */
    protected string $action;

    /**
     * @param RepositoryInterface $repository
     * @param Model|null $model
     */
    public function __construct(RepositoryInterface $repository, Model|array|null $model = null)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @return Model|array|null
     */
    public function getModel(): Model|array|null
    {
        return $this->model;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
