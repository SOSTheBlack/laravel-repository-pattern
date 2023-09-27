<?php

namespace SOSTheBlack\Repository\Validators;

use Illuminate\Contracts\Validation\Factory;

final class LaravelValidator extends AbstractValidator
{
    /**
     * Validator
     *
     * @var Factory
     */
    protected Factory $validator;

    /**
     * Construct
     *
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @param string|null $action
     * @return bool
     */
    public function passes(?string $action = null): bool
    {
        $rules = $this->getRules($action);
        $messages = $this->getMessages();
        $attributes = $this->getAttributes();
        $validator = $this->validator->make($this->data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            $this->errors = $validator->messages();
            return false;
        }

        return true;
    }
}
