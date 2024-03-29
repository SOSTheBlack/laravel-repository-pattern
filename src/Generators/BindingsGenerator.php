<?php

namespace SOSTheBlack\Repository\Generators;

/**
 * Class BindingsGenerator.
 */
class BindingsGenerator extends Generator
{

    /**
     * The placeholder for repository bindings
     *
     * @var string
     */
    public string $bindPlaceholder = '//:end-bindings:';
    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'bindings/bindings';

    public function run(): mixed
    {
        // Add entity repository binding to the repository service provider
        $provider = \File::get($this->getPath());
        $repositoryInterface = '\\' . $this->getRepository() . "::class";
        $repositoryEloquent = '\\' . $this->getEloquentRepository() . "::class";
        \File::put($this->getPath(), str_replace($this->bindPlaceholder, "\$this->app->bind({$repositoryInterface}, $repositoryEloquent);" . PHP_EOL . '        ' . $this->bindPlaceholder, $provider));
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . '/Providers/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '.php';
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
        return 'provider';
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

        return str_replace([
                "\\",
                '/'
            ], '\\', $repository) . 'Repository';
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
     * Gets eloquent repository full class name
     *
     * @return string
     */
    public function getEloquentRepository(): string
    {
        $repositoryGenerator = new RepositoryEloquentGenerator([
            'name' => $this->name,
        ]);

        $repository = $repositoryGenerator->getRootNamespace() . '\\' . $repositoryGenerator->getName();

        return str_replace([
                "\\",
                '/'
            ], '\\', $repository) . 'RepositoryEloquent';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements(): array
    {

        return array_merge(parent::getReplacements(), [
            'repository' => $this->getRepository(),
            'eloquent' => $this->getEloquentRepository(),
            'placeholder' => $this->bindPlaceholder,
        ]);
    }
}
