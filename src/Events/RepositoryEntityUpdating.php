<?php
namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityUpdated
 * @package SOSTheBlack\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityUpdating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updating";
}
