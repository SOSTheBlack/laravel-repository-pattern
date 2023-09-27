<?php

namespace SOSTheBlack\Repository\Generators\Migrations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class RulesParser.\Migrations
 */
class RulesParser implements Arrayable
{

    /**
     * The set of rules.
     *
     * @var string|null
     */
    protected ?string $rules;


    /**
     * Create new instance.
     *
     * @param string|null $rules
     */
    public function __construct(?string $rules = null)
    {
        $this->rules = $rules;
    }

    /**
     * Convert string migration to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->parse($this->rules);
    }

    /**
     * Parse a string to array of formatted rules.
     *
     * @param string $rules
     *
     * @return array
     */
    public function parse(string $rules): array
    {
        $this->rules = $rules;
        $parsed = [];
        foreach ($this->getRules() as $rulesArray) {
            $column = $this->getColumn($rulesArray);
            $attributes = $this->getAttributes($column, $rulesArray);
            $parsed[$column] = $attributes;
        }

        return $parsed;
    }

    /**
     * Get array of rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        if (is_null($this->rules)) {
            return [];
        }

        return explode(',', str_replace(' ', '', $this->rules));
    }

    /**
     * Get column name from rules.
     *
     * @param string $rules
     *
     * @return string
     */
    public function getColumn(string $rules): string
    {
        return Arr::first(explode('=>', $rules), function ($key, $value) {
            return $value;
        });
    }


    /**
     * Get column attributes.
     *
     * @param string $column
     * @param string $rules
     *
     * @return array
     */
    public function getAttributes(string $column, string $rules): array
    {

        return str_replace($column . '=>', '', $rules);
    }

}
