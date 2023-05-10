<?php

namespace SOSTheBlack\Repository\Contracts;

/**
 * Interface Presentable

 * @package SOSTheBlack\Repository\Contracts
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
interface Presentable
{
    /**
     * @param PresenterInterface $presenter
     *
     * @return mixed
     */
    public function setPresenter(PresenterInterface $presenter): mixed;

    /**
     * @return mixed
     */
    public function presenter(): mixed;
}
