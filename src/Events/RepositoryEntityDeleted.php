<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityDeleted.
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_DELETE;
}
