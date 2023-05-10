<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityUpdated

 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityUpdating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = self::ACTION_UPDATING;
}
