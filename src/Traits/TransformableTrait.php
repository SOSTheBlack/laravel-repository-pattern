<?php

namespace SOSTheBlack\Repository\Traits;

/**
 * Class TransformableTrait.
 * @package SOSTheBlack\Repository\Traits
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
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
