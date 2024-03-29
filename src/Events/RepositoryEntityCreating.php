<?php

namespace SOSTheBlack\Repository\Events;

use SOSTheBlack\Repository\Contracts\RepositoryInterface;

/**
 * Class RepositoryEntityCreated
 *.
 */
class RepositoryEntityCreating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_CREATING;

    public function __construct(RepositoryInterface $repository, array $model)
    {
        parent::__construct($repository, $model);

        $this->model = $model;
    }
}
