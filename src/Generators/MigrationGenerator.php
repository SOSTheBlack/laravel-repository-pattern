<?php

namespace SOSTheBlack\Repository\Generators;

use SOSTheBlack\Repository\Generators\Migrations\NameParser;
use SOSTheBlack\Repository\Generators\Migrations\SchemaParser;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Class MigrationGenerator.
 */
class MigrationGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'migration/plain';

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . $this->getFileName() . '.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return base_path() . '/database/migrations/';
    }

    /**
     * Get file name.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return date('Y_m_d_His_') . $this->getMigrationName();
    }

    /**
     * Get migration name.
     *
     * @return string
     */
    public function getMigrationName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode(): string
    {
        return '';
    }

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return '';
    }

    /**
     * Get stub templates.
     *
     * @return string
     */
    public function getStub(): string
    {
        $parser = $this->getNameParser();

        $action = $parser->getAction();
        switch ($action) {
            case 'add':
            case 'append':
            case 'update':
            case 'insert':
                $file = 'change';
                $replacements = [
                    'class' => $this->getClass(),
                    'table' => $parser->getTable(),
                    'fields_up' => $this->getSchemaParser()->up(),
                    'fields_down' => $this->getSchemaParser()->down(),
                ];
                break;

            case 'delete':
            case 'remove':
            case 'alter':
                $file = 'change';
                $replacements = [
                    'class' => $this->getClass(),
                    'table' => $parser->getTable(),
                    'fields_down' => $this->getSchemaParser()->up(),
                    'fields_up' => $this->getSchemaParser()->down(),
                ];
                break;
            default:
                $file = 'create';
                $replacements = [
                    'class' => $this->getClass(),
                    'table' => $parser->getTable(),
                    'fields' => $this->getSchemaParser()->up(),
                ];
                break;
        }
        $path = config('repository.generator.stubsOverridePath', __DIR__);

        if (!file_exists($path . "/Stubs/migration/{$file}.stub")) {
            $path = __DIR__;
        }

        if (!file_exists($path . "/Stubs/migration/{$file}.stub")) {
            throw new FileNotFoundException($path . "/Stubs/migration/{$file}.stub");
        }

        return Stub::create($path . "/Stubs/migration/{$file}.stub", $replacements);
    }

    /**
     * Get name parser.
     *
     * @return NameParser
     */
    public function getNameParser(): NameParser
    {
        return new NameParser($this->name);
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser(): SchemaParser
    {
        return new SchemaParser($this->fields);
    }
}
