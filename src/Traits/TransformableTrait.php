<?php

namespace SOSTheBlack\Repository\Traits;

/**
 * Class TransformableTrait.
 * @package SOSTheBlack\Repository\Traits
 */
trait TransformableTrait
{
    /**
     * @return array
     */
    public function transform(): array
    {
        return $this->toArray();
    }
}
