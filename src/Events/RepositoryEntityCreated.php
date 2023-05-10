<?php
namespace SOSTheBlack\Repository\Events;

/**
 * Class RepositoryEntityCreated
 * @package SOSTheBlack\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "created";
}
