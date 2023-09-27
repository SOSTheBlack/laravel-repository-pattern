<?php

namespace SOSTheBlack\Repository\Generators;

use SOSTheBlack\Repository\Generators\Migrations\RulesParser;
use SOSTheBlack\Repository\Generators\Migrations\SchemaParser;

/**
 * Class ValidatorGenerator
 */
class ValidatorGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'validator/validator';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode(): string
    {
        return 'validators';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Validator.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return config('repository.generator.basePath', app()->path());
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements(): array
    {

        return array_merge(parent::getReplacements(), [
            'rules' => $this->getRules(),
        ]);
    }

    /**
     * Get the rules.
     *
     * @return string
     */
    public function getRules(): string
    {
        if (!$this->rules) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'\t=>'\t{$value}'," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser(): RulesParser|SchemaParser
    {
        return new RulesParser($this->rules);
    }
}
