<?php

namespace SOSTheBlack\Repository\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use SOSTheBlack\Repository\Exceptions\ValidatorException;

interface ValidatorInterface
{
    const RULE_CREATE = 'create';
    const RULE_UPDATE = 'update';

    /**
     * Set Id
     *
     * @param $id
     * @return $this
     */
    public function setId($id): static;

    /**
     * With
     *
     * @param array $input
     *
     * @return $this
     */
    public function with(array $input): static;

    /**
     * Pass the data and the rules to the validator
     *
     * @param string|null $action
     * @return boolean
     */
    public function passes(?string $action = null): bool;

    /**
     * Pass the data and the rules to the validator or throws ValidatorException
     *
     * @param string|null $action
     * @return boolean
     *@throws ValidatorException
     */
    public function passesOrFail(?string $action = null): bool;

    /**
     * Errors
     *
     * @return array
     */
    public function errors(): array;

    /**
     * Errors
     *
     * @return MessageBag
     */
    public function errorsBag(): MessageBag;

    /**
     * Set Rules for Validation
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules): static;

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE
     *
     * Default rule: ValidatorInterface::RULE_CREATE
     *
     * @param $action
     *
     * @return array
     */
    public function getRules($action = null): array;

    /**
     * Set Custom error messages for Validation
     *
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages): static;

    /**
     * Set Custom error attributes for Validation
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): static;
}
