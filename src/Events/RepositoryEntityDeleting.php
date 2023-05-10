<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityDeleted

 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleting";
}
