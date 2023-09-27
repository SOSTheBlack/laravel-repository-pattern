<?php

namespace SOSTheBlack\Repository\Generators;

/**
 * Class PresenterGenerator.
 */
class PresenterGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected string $stub = 'presenter/presenter';

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements(): array
    {
        $transformerGenerator = new TransformerGenerator([
            'name' => $this->name
        ]);
        $transformer = $transformerGenerator->getRootNamespace() . '\\' . $transformerGenerator->getName() . 'Transformer';
        $transformer = str_replace([
            "\\",
            '/'
        ], '\\', $transformer);
        echo $transformer;

        return array_merge(parent::getReplacements(), [
            'transformer' => $transformer
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
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode(): string
    {
        return 'presenters';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Presenter.php';
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
}
