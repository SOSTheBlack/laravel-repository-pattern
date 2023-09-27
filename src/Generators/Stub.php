<?php

namespace SOSTheBlack\Repository\Generators;

/**
 * Class Stub.
 */
class Stub
{
    /**
     * The base path of stub file.
     *
     * @var null|string
     */
    protected static ?string $basePath = null;
    /**
     * The stub path.
     *
     * @var string
     */
    protected string $path;
    /**
     * The replacements array.
     *
     * @var array
     */
    protected array $replaces = [];

    /**
     * The contructor.
     *
     * @param  string  $path
     * @param array    $replaces
     */
    public function __construct(string $path, array $replaces = [])
    {
        $this->path = $path;
        $this->replaces = $replaces;
    }

    /**
     * Create new self instance.
     *
     * @param  string  $path
     * @param array    $replaces
     *
     * @return self
     */
    public static function create(string $path, array $replaces = []): Stub
    {
        return new static($path, $replaces);
    }

    /**
     * Set base path.
     *
     * @param  string  $path
     *
     * @return void
     */
    public static function setBasePath(string $path): void
    {
        static::$basePath = $path;
    }

    /**
     * Set replacements array.
     *
     * @param array $replaces
     *
     * @return $this
     */
    public function replace(array $replaces = []): static
    {
        $this->replaces = $replaces;

        return $this;
    }

    /**
     * Get replacements.
     *
     * @return array
     */
    public function getReplaces(): array
    {
        return $this->replaces;
    }

    /**
     * Handle magic method __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Get stub contents.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->getContents();
    }

    /**
     * Get stub contents.
     *
     * @return mixed
     */
    public function getContents(): mixed
    {
        $contents = file_get_contents($this->getPath());
        foreach ($this->replaces as $search => $replace) {
            $contents = str_replace('$' . strtoupper($search) . '$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get stub path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return static::$basePath . $this->path;
    }

    /**
     * Set stub path.
     *
     * @param  string  $path
     *
     * @return self
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }
}
