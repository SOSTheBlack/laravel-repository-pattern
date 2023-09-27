<?php

namespace SOSTheBlack\Repository\Validators;

use Illuminate\Support\MessageBag;
use SOSTheBlack\Repository\Contracts\ValidatorInterface;
use SOSTheBlack\Repository\Exceptions\ValidatorException;
use Illuminate\Contracts\Validation\Factory;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var ?int
     */
    protected ?int $id = null;

    /**
     * Validator
     *
     * @var Factory
     */
    protected Factory $validator;

    /**
     * Data to be validated
     *
     * @var array
     */
    protected array $data = array();

    /**
     * Validation Rules
     *
     * @var array
     */
    protected array $rules = array();

    /**
     * Validation Custom Messages
     *
     * @var array
     */
    protected array $messages = array();

    /**
     * Validation Custom Attributes
     *
     * @var array
     */
    protected array $attributes = array();

    /**
     * Validation errors
     *
     * @var array|MessageBag
     */
    protected array|MessageBag $errors = array();


    /**
     * Set Id
     *
     * @param $id
     * @return $this
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set data to validate
     *
     * @param array $data
     *
     * @return $this
     */
    public function with(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Return errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errorsBag()->all();
    }

    /**
     * Errors
     *
     * @return MessageBag
     */
    public function errorsBag(): MessageBag
    {
        return $this->errors;
    }

    /**
     * Pass the data and the rules to the validator or throws ValidatorException
     *
     * @param string|null $action
     *
     * @return boolean
     *
     * @throws ValidatorException
     */
    public function passesOrFail(?string $action = null): bool
    {
        if (!$this->passes($action)) {
            throw new ValidatorException($this->errorsBag());
        }

        return true;
    }

    /**
     * Pass the data and the rules to the validator
     *
     * @param string|null $action
     *
     * @return boolean
     */
    abstract public function passes(?string $action = null): bool;

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE
     *
     * Default rule: ValidatorInterface::RULE_CREATE
     *
     * @param null $action
     * @return array
     */
    public function getRules($action = null): array
    {
        $rules = $this->rules;

        if (isset($this->rules[$action])) {
            $rules = $this->rules[$action];
        }

        return $this->parserValidationRules($rules, $this->id);
    }

    /**
     * Set Rules for Validation
     *
     * @param array $rules
     * @return $this
     */
    public function setRules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Parser Validation Rules
     *
     * @param $rules
     * @param null $id
     * @return array
     */
    protected function parserValidationRules($rules, $id = null): array
    {
        if (null === $id) {
            return $rules;
        }

        array_walk($rules, function (&$rules, $field) use ($id) {
            if (!is_array($rules)) {
                $rules = explode("|", $rules);
            }

            foreach ($rules as $ruleIdx => $rule) {
                // get name and parameters
                @list($name, $params) = array_pad(explode(":", $rule), 2, null);

                // only do someting for the unique rule
                if (strtolower($name) != "unique") {
                    continue; // continue in foreach loop, nothing left to do here
                }

                $p = array_map("trim", explode(",", $params));

                // set field name to rules key ($field) (laravel convention)
                if (!isset($p[1])) {
                    $p[1] = $field;
                }

                // set 3rd parameter to id given to getValidationRules()
                $p[2] = $id;

                $params = implode(",", $p);
                $rules[$ruleIdx] = $name . ":" . $params;
            }
        });

        return $rules;
    }

    /**
     * Get Custom error messages for validation
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set Custom error messages for Validation
     *
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages): static
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Get Custom error attributes for validation
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set Custom error attributes for Validation
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }
}
