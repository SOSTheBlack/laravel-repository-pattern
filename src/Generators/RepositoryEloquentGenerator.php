<?php

namespace SOSTheBlack\Repository\Generators;

use SOSTheBlack\Repository\Generators\Migrations\SchemaParser;

/**
 * Class RepositoryEloquentGenerator.
 */
class RepositoryEloquentGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'repository/eloquent';

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'RepositoryEloquent.php';
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
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode(): string
    {
        return 'repositories';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements(): array
    {
        $repository = parent::getRootNamespace() . parent::getConfigGeneratorClassPath('interfaces') . '\\' . $this->name . 'Repository;';
        $repository = str_replace([
            "\\",
            '/'
        ], '\\', $repository);

        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'use_validator' => $this->getValidatorUse(),
            'validator' => $this->getValidatorMethod(),
            'repository' => $repository,
            'model' => isset($this->options['model']) ? $this->options['model'] : ''
        ]);
    }

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
     * Get the fillable attributes.
     *
     * @return string
     */
    public function getFillable(): string
    {
        if (!$this->fillable) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser(): SchemaParser
    {
        return new SchemaParser($this->fillable);
    }

    public function getValidatorUse(): string
    {
        $validator = $this->getValidator();

        return "use {$validator};";
    }


    public function getValidator(): string
    {
        $validatorGenerator = new ValidatorGenerator([
            'name' => $this->name,
            'rules' => $this->rules,
            'force' => $this->force,
        ]);

        $validator = $validatorGenerator->getRootNamespace() . '\\' . $validatorGenerator->getName();

        return str_replace([
                "\\",
                '/'
            ], '\\', $validator) . 'Validator';

    }


    public function getValidatorMethod(): string
    {
        if ($this->validator != 'yes') {
            return '';
        }

        $class = $this->getClass();

        return '/**' . PHP_EOL . '    * Specify Validator class name' . PHP_EOL . '    *' . PHP_EOL . '    * @return mixed' . PHP_EOL . '    */' . PHP_EOL . '    public function validator()' . PHP_EOL . '    {' . PHP_EOL . PHP_EOL . '        return ' . $class . 'Validator::class;' . PHP_EOL . '    }' . PHP_EOL;

    }
}
