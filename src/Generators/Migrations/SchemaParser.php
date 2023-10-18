<?php

namespace SOSTheBlack\Repository\Generators\Migrations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class SchemaParser.\Migrations
 */
class SchemaParser implements Arrayable
{
    /**
     * The array of custom attributes.
     *
     * @var array
     */
    protected array $customAttributes = [
        'remember_token' => 'rememberToken()',
        'soft_delete' => 'softDeletes()',
    ];
    /**
     * The migration schema.
     *
     * @var string|null
     */
    protected ?string $schema;

    /**
     * Create new instance.
     *
     * @param string|null $schema
     */
    public function __construct(?string $schema = null)
    {
        $this->schema = $schema;
    }

    /**
     * Render up migration fields.
     *
     * @return string
     */
    public function up(): string
    {
        return $this->render();
    }

    /**
     * Render the migration to formatted script.
     *
     * @return string
     */
    public function render(): string
    {
        $results = '';
        foreach ($this->toArray() as $column => $attributes) {
            $results .= $this->createField($column, $attributes);
        }

        return $results;
    }

    /**
     * Convert string migration to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->parse($this->schema);
    }

    /**
     * Parse a string to array of formatted schema.
     *
     * @param string|null $schema
     *
     * @return array
     */
    public function parse(?string $schema): array
    {
        $this->schema = $schema;
        $parsed = [];
        foreach ($this->getSchemas() as $schemaArray) {
            $column = $this->getColumn($schemaArray);
            $attributes = $this->getAttributes($column, $schemaArray);
            $parsed[$column] = $attributes;
        }

        return $parsed;
    }

    /**
     * Get array of schema.
     *
     * @return array
     */
    public function getSchemas(): array
    {
        if (is_null($this->schema)) {
            return [];
        }

        return explode(',', str_replace(' ', '', $this->schema));
    }

    /**
     * Get column name from schema.
     *
     * @param string $schema
     *
     * @return string
     */
    public function getColumn(string $schema): string
    {
        return Arr::first(explode(':', $schema), function ($key, $value) {
            return $value;
        });
    }

    /**
     * Get column attributes.
     *
     * @param string $column
     * @param string $schema
     *
     * @return array
     */
    public function getAttributes(string $column, string $schema): array
    {
        $fields = str_replace($column . ':', '', $schema);

        return $this->hasCustomAttribute($column) ? $this->getCustomAttribute($column) : explode(':', $fields);
    }

    /**
     * Determinte whether the given column is exist in customAttributes array.
     *
     * @param string $column
     *
     * @return boolean
     */
    public function hasCustomAttribute(string $column): bool
    {
        return array_key_exists($column, $this->customAttributes);
    }

    /**
     * Get custom attributes value.
     *
     * @param string $column
     *
     * @return array
     */
    public function getCustomAttribute(string $column): array
    {
        return (array)$this->customAttributes[$column];
    }

    /**
     * Create field.
     *
     * @param string $column
     * @param array $attributes
     * @param string $type
     *
     * @return string
     */
    public function createField(string $column, array $attributes, string $type = 'add'): string
    {
        $results = "\t\t\t" . '$table';
        foreach ($attributes as $key => $field) {
            $results .= $this->{"{$type}Column"}($key, $field, $column);
        }

        return $results .= ';' . PHP_EOL;
    }

    /**
     * Render down migration fields.
     *
     * @return string
     */
    public function down(): string
    {
        $results = '';
        foreach ($this->toArray() as $column => $attributes) {
            $results .= $this->createField($column, $attributes, 'remove');
        }

        return $results;
    }

    /**
     * Format field to script.
     *
     * @param int $key
     * @param string $field
     * @param string $column
     *
     * @return string
     */
    protected function addColumn(int $key, string $field, string $column): string
    {
        if ($this->hasCustomAttribute($column)) {
            return '->' . $field;
        }
        if ($key == 0) {
            return '->' . $field . "('" . $column . "')";
        }
        if (Str::contains($field, '(')) {
            return '->' . $field;
        }

        return '->' . $field . '()';
    }

    /**
     * Format field to script.
     *
     * @param int $key
     * @param string $field
     * @param string $column
     *
     * @return string
     */
    protected function removeColumn(int $key, string $field, string $column): string
    {
        if ($this->hasCustomAttribute($column)) {
            return '->' . $field;
        }

        return '->dropColumn(' . "'" . $column . "')";
    }
}
