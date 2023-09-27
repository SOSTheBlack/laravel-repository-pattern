<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityCreated.
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_CREATED;
}
