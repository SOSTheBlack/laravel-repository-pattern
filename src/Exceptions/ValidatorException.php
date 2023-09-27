<?php

namespace SOSTheBlack\Repository\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\MessageBag;
use Throwable;

class ValidatorException extends Exception implements Jsonable, Arrayable, Throwable
{
    /**
     * @var MessageBag
     */
    protected MessageBag $messageBag;

    /**
     * @param MessageBag $messageBag
     */
    public function __construct(MessageBag $messageBag)
    {
        $this->messageBag = $messageBag;
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag(): MessageBag
    {
        return $this->messageBag;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'error'=>'validation_exception',
            'error_description'=>$this->getMessageBag()
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
