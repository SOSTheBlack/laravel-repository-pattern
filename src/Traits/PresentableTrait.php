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
     * @var PresenterInterface|null
     */
    protected ?PresenterInterface $presenter = null;

    /**
     * @param PresenterInterface $presenter
     *
     * @return $this
     */
    public function setPresenter(PresenterInterface $presenter): static
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
    public function present($key, $default = null): mixed
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
    protected function hasPresenter(): bool
    {
        return isset($this->presenter) && $this->presenter instanceof PresenterInterface;
    }

    /**
     * @return null|static
     */
    public function presenter(): null|static
    {
        if ($this->hasPresenter()) {
            return $this->presenter->present($this);
        }

        return $this;
    }
}
