<?php

namespace SOSTheBlack\Repository\Contracts;

/**
 * Interface Presentable.
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
