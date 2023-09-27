<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityUpdated.
 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = "updated";
}
