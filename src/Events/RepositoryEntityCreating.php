<?php

namespace SOSTheBlack\Repository\Events;

use SOSTheBlack\Repository\Contracts\RepositoryInterface;

/**
 * Class RepositoryEntityCreated
 *

 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityCreating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "creating";

    public function __construct(RepositoryInterface $repository, array $model)
    {
        parent::__construct($repository);
        $this->model = $model;
    }
}
