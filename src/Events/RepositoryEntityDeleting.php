<?php
namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityDeleted
 * @package SOSTheBlack\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleting";
}
