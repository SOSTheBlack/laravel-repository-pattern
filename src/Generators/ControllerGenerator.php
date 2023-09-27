<?php

namespace SOSTheBlack\Repository\Generators;

use Illuminate\Support\Str;

/**
 * Class ControllerGenerator.
 */
class ControllerGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'controller/controller';

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getControllerName() . 'Controller.php';
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
        return 'controllers';
    }

    /**
     * Gets controller name based on model
     *
     * @return string
     */
    public function getControllerName(): string
    {

        return ucfirst($this->getPluralName());
    }

    /**
     * Gets plural name based on model
     *
     * @return string
     */
    public function getPluralName(): string
    {

        return Str::plural(lcfirst(ucwords($this->getClass())));
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements(): array
    {

        return array_merge(parent::getReplacements(), [
            'controller' => $this->getControllerName(),
            'plural' => $this->getPluralName(),
            'singular' => $this->getSingularName(),
            'validator' => $this->getValidator(),
            'repository' => $this->getRepository(),
            'appname' => $this->getAppNamespace(),
        ]);
    }

    /**
     * Gets singular name based on model
     *
     * @return string
     */
    public function getSingularName(): string
    {
        return Str::singular(lcfirst(ucwords($this->getClass())));
    }

    /**
     * Gets validator full class name
     *
     * @return string
     */
    public function getValidator(): string
    {
        $validatorGenerator = new ValidatorGenerator([
            'name' => $this->name,
        ]);

        $validator = $validatorGenerator->getRootNamespace() . '\\' . $validatorGenerator->getName();

        return 'use ' . str_replace([
                "\\",
                '/'
            ], '\\', $validator) . 'Validator;';
    }

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return str_replace('/', '\\', parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode()));
    }

    /**
     * Gets repository full class name
     *
     * @return string
     */
    public function getRepository(): string
    {
        $repositoryGenerator = new RepositoryInterfaceGenerator([
            'name' => $this->name,
        ]);

        $repository = $repositoryGenerator->getRootNamespace() . '\\' . $repositoryGenerator->getName();

        return 'use ' . str_replace([
                "\\",
                '/'
            ], '\\', $repository) . 'Repository;';
    }
}
