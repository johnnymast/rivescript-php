<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript\ContentLoader;

use Axiom\Rivescript\Exceptions\ContentLoader\ContentLoaderException;
use DirectoryIterator;
use SplFileObject;

/**
 * ContentLoader class
 *
 * The ContentLoader assists in loading information
 * into the Rivescript interpreter. The data will be
 * store in a stream for the brain to learn
 * from.
 *
 * PHP version 8.1 and higher.
 *
 * @see      https://www.php.net/manual/en/stream.streamwrapper.example-1.php
 * @see      https://www.php.net/manual/en/class.streamwrapper
 *
 * @category Core
 * @package  Cortext\ContentLoader
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ContentLoader
{
    /**
     * The stream resource.
     *
     * @var mixed
     */
    private mixed $stream = null;

    /**
     * The stream wrapper name.
     *
     * @var string
     */
    protected string $name = 'rivescript';

    /**
     * ContentLoader constructor.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoader\ContentLoaderException
     */
    public function __construct()
    {
        $this->openStream();
    }

    /**
     *  ContentLoader deconstruct.
     */
    public function __destruct()
    {
        if (is_resource($this->getStream())) {
            fclose($this->getStream());
        }
    }

    /**
     * Return the stream.
     *
     * @return mixed
     */
    public function getStream(): mixed
    {
        return $this->stream;
    }

    /**
     * Load strings / files or whole directories
     * into the brain.
     *
     * @param string|array $path The path to load, this could be a string or an array of strings.
     *
     * @return void
     */
    public function load(string|array $path): void
    {
        if (is_string($path) === true && file_exists($path) === false) {
            $this->writeToMemory($path);
        } elseif (is_string($path) === true && is_dir($path) === true) {
            $this->loadDirectory($path);
        } elseif (is_string($path) === true && file_exists($path) === true) {
            $this->loadFile($path);
        } elseif (is_array($path) === true && count($path) > 0) {
            foreach ($path as $file) {
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
    public function loadDirectory(string $path): void
    {
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $this->loadFile($file->getPathname());
        }
    }

    /**
     * Load a single file into memory.
     *
     * @param string $filename The Rivescript file to load.
     *
     * @return void
     */
    public function loadFile(string $filename): void
    {
        $file = new SplFileObject($filename, "rb");

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
    public function writeToMemory(string $text): void
    {
        if ($this->stream) {
            fwrite($this->stream, $text);
        }
    }

    /**
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     * @return void
     */
    public function openStream(): void
    {
        $existed = in_array($this->name, stream_get_wrappers(), true);

        if ($existed) {
            stream_wrapper_unregister($this->name);
        }
        if (stream_wrapper_register($this->name, ContentStream::class) === true) {
            $this->stream = fopen($this->name . "://input", 'wb+');
        }

        if (is_resource($this->stream) === false) {
            throw new ContentLoaderException("Could not instantiate new rivescript content stream.");
        }
    }

    /**
     * Close the stream.
     *
     * @return void
     */
    public function closeStream(): void
    {
        fclose($this->getStream());
    }
}
