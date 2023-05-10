<?php

namespace SOSTheBlack\Repository\Contracts;

/**
 * Interface Transformable

 * @package SOSTheBlack\Repository\Contracts
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
interface Transformable
{
    /**
     * @return array
     */
    public function transform(): array;
}
