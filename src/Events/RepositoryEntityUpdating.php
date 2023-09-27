<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityUpdated.
 */
class RepositoryEntityUpdating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_UPDATING;
}
