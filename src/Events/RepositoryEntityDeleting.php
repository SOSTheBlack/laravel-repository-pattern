<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityDeleted.
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_DELETING;
}
