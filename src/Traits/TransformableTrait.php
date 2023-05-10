<?php

namespace SOSTheBlack\Repository\Traits;

/**
 * Class TransformableTrait
 * @package SOSTheBlack\Repository\Traits
 * @author Anderson Andrade <contato@andersonandra.de>
 */
trait TransformableTrait
{
    /**
     * @return array
     */
    public function transform()
    {
        return $this->toArray();
    }
}
