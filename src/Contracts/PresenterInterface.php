<?php

namespace SOSTheBlack\Repository\Contracts;

/**
 * Interface PresenterInterface
 * @package SOSTheBlack\Repository\Contracts
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
interface PresenterInterface
{
    /**
     * Prepare data to present
     *
     * @param $data
     *
     * @return mixed
     */
    public function present($data): mixed;
}
