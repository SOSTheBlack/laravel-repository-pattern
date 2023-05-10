<?php

namespace SOSTheBlack\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RepositoryEventBaseInterface
{
    public const ACTION_CREATED = 'created';

    public const ACTION_CREATING = 'creating';

    public const ACTION_DELETE = 'deleted';

    public const ACTION_DELETING = 'deleting';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_UPDATING = 'updating';

    /**
     * @return Model|array|null
     */
    public function getModel(): Model|array|null;

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * @return string
     */
    public function getAction(): string;
}
