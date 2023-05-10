<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityDeleted
 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_DELETE;
}
