<?php

namespace SOSTheBlack\Repository\Traits;

use Illuminate\Support\Arr;
use SOSTheBlack\Repository\Contracts\PresenterInterface;

/**
 * Class PresentableTrait


 * @package SOSTheBlack\Repository\Traits
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
trait PresentableTrait
{

    /**
     * @var PresenterInterface
     */
    protected $presenter = null;

    /**
     * @param \SOSTheBlack\Repository\Contracts\PresenterInterface $presenter
     *
     * @return $this
     */
    public function setPresenter(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function present($key, $default = null)
    {
        if ($this->hasPresenter()) {
            $data = $this->presenter()['data'];

            return Arr::get($data, $key, $default);
        }

        return $default;
    }

    /**
     * @return bool
     */
    protected function hasPresenter()
    {
        return isset($this->presenter) && $this->presenter instanceof PresenterInterface;
    }

    /**
     * @return $this|mixed
     */
    public function presenter()
    {
        if ($this->hasPresenter()) {
            return $this->presenter->present($this);
        }

        return $this;
    }
}
