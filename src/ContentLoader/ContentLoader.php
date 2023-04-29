<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\ContentLoader;

use Axiom\Rivescript\Exceptions\ContentLoadingException;
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
 * PHP version 7.4 and higher.
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
    private $stream;

    /**
     * The stream wrapper name.
     *
     * @var string
     */
    protected string $name = 'rivescript';

    /**
     * Flag indicating if the stream-wrapper
     * was successfully registered.
     *
     * @var bool
     */
    protected bool $isRegistered = false;

    /**
     * ContentLoader constructor.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct()
    {
        $existed = in_array($this->name, stream_get_wrappers(), true);
        if ($existed) {
            stream_wrapper_unregister($this->name);
        }
        if (stream_wrapper_register($this->name, ContentStream::class) === true) {
            $this->stream = fopen($this->name . "://input", 'wb+');
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
        if (is_resource($this->getStream())) {
            fclose($this->getStream());
        }
    }

    /**
     * Return the stream.
     *
     * @return mixed
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Load strings / files or whole directories
     * into the brain.
     *
     * @param mixed $info Information about what to load.
     *
     * @return void
     */
    public function load($info): void
    {
        if (is_string($info) === true && file_exists($info) === false) {
            $this->writeToMemory($info);
        } elseif (is_string($info) === true && is_dir($info) === true) {
            $this->loadDirectory($info);
        } elseif (is_string($info) === true && file_exists($info) === true) {
            $this->loadFile($info);
        } elseif (is_array($info) === true && count($info) > 0) {
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
}
