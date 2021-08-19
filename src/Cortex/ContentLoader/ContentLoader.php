<?php

namespace Axiom\Rivescript\Cortex\ContentLoader;

use Axiom\Rivescript\Exceptions\ContentLoadingException;
use DirectoryIterator;
use SplFileObject;

/**
 * The ContentLoader assists in loading information
 * into the Rivescript interpreter. The data will be
 * store in a stream for the brain to learn
 * from.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     ContentLoader
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

/**
 * ContentLoader class.
 */
class ContentLoader
{
    /**
     * The stream resource.
     *
     * @var false|resource
     */
    private $stream;

    /**
     * The stream wrapper name.
     *
     * @var string
     */
    protected $name = 'rivescript';

    /**
     * ContentLoader constructor.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct()
    {
        $existed = in_array($this->name, stream_get_wrappers());
        if ($existed) {
            stream_wrapper_unregister($this->name);
        }

        if (stream_wrapper_register($this->name, ContentStream::class)) {
            $this->stream = fopen($this->name . "://input", "r+");
        }

        if (is_resource($this->stream) === false) {
            throw new ContentLoadingException("Could not instantiate new rivescript content stream.");
        }
    }

    /**
     *  ContentLoader deconstruct.
     */
    public function __destruct()
    {
        fclose($this->getStream());
    }

    /**
     * Return the stream.
     *
     * @return false|resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Load strings / files or whole directories
     * into the brain.
     *
     * @param mixed $info
     *
     * @return void
     */
    public function load($info)
    {
        if (is_string($info) === true && file_exists($info) === false) {
            $this->writeToMemory($info);
        } elseif (is_string($info) === true && is_dir($info) === true) {
            $this->loadDirectory($info);
        } elseif (is_string($info) === true && file_exists($info) === true) {
            $this->loadFile($info);
        } elseif (is_array($info) === true and count($info) > 0) {
            foreach ($info as $file) {
                if (file_exists($file)) {
                    $this->loadFile($file);
                }
            }
        }
    }

    /**
     * Load rivescript files from a given path.
     *
     * @param string $path Path to the directory to load.
     *
     * @return void
     */
    public function loadDirectory(string $path)
    {
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot())
                continue;

            $this->loadFile($file->getPathname());
        }
    }

    /**
     * Load a single file into memory.
     *
     * @param string $file The Rivescript file to load.
     *
     * @return void
     */
    public function loadFile(string $file)
    {
        $file = new SplFileObject($file);

        while (!$file->eof()) {
            $line = $file->fgets();
            $this->writeToMemory($line);
        }
    }

    /**
     * Write rivescript interpretable strings into
     * its collective brain.
     *
     * @param string $text The text to write to memory.
     *
     * @return void
     */
    public function writeToMemory(string $text)
    {
        if ($this->stream) {
            fwrite($this->stream, $text);
        }
    }
}
