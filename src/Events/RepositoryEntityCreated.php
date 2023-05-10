<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityCreated

 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = SELF::ACTION_CREATED;
}
