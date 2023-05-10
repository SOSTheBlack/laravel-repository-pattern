<?php

namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityUpdated

 * @package SOSTheBlack\Repository\Events
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected string $action = "updated";
}
