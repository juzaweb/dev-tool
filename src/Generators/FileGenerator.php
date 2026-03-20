<?php

namespace Juzaweb\DevTool\Generators;

use Illuminate\Filesystem\Filesystem;
use Juzaweb\Modules\Core\Modules\Exceptions\FileAlreadyExistException;

class FileGenerator extends Generator
{
    /**
     * The path wil be used.
     */
    protected string $path;

    /**
     * The contens will be used.
     */
    protected string $contents;

    /**
     * The laravel filesystem or null.
     */
    protected ?Filesystem $filesystem;

    private bool $overwriteFile = false;

    /**
     * The constructor.
     *
     * @param  null  $filesystem
     */
    public function __construct($path, $contents, $filesystem = null)
    {
        $this->path = $path;
        $this->contents = $contents;
        $this->filesystem = $filesystem ?: new Filesystem;
    }

    /**
     * Get contents.
     *
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set contents.
     *
     *
     * @return $this
     */
    public function setContents(mixed $contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get filesystem.
     *
     * @return mixed
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Set filesystem.
     *
     *
     * @return $this
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Get path.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     *
     * @return $this
     */
    public function setPath(mixed $path)
    {
        $this->path = $path;

        return $this;
    }

    public function withFileOverwrite(bool $overwrite): FileGenerator
    {
        $this->overwriteFile = $overwrite;

        return $this;
    }

    /**
     * Generate the file.
     */
    public function generate()
    {
        $path = $this->getPath();
        if (! $this->filesystem->exists($path)) {
            return $this->filesystem->put($path, $this->getContents());
        }
        if ($this->overwriteFile === true) {
            return $this->filesystem->put($path, $this->getContents());
        }

        throw new FileAlreadyExistException('File already exists!');
    }
}
