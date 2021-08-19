<?php

/**
 * ContentStream handles all the information being loaded
 * into the Rivescript interpreter.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     ContentLoader
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\ContentLoader;

/**
 * This class taken from php.net and changed
 * to be workable for this project.
 *
 * @see https://www.php.net/manual/en/stream.streamwrapper.example-1.php
 * @see https://www.php.net/manual/en/class.streamwrapper
 */
class ContentStream
{
    /**
     * The cursor position inside the stream.
     *
     * @var int
     */
    private $position;

    /**
     * The variable that holds
     * the contents of the stream.
     *
     * @var string
     */
    protected $content;

    /**
     * Specifies the URL that was passed to the original function.
     * This is currently not used.
     *
     * @var string
     */
    protected $path;

    /**
     * The mode to operate in.
     * This is currently not used.
     *
     * @var string
     */
    protected $mode;

    /**
     * @param string $path Specifies the URL that was passed to the original function.
     * @param string $mode The mode used to open the file, as detailed for fopen().
     *
     * @see https://www.php.net/manual/en/function.fopen.php
     *
     * @return bool
     */
    public function stream_open(string $path, string $mode): bool
    {
        $this->content = '';
        $this->position = 0;
        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param int $count
     *
     * @return string
     */
    public function stream_read(int $count): string
    {
        $p =& $this->position;
        $ret = substr($this->content, $p, $count);
        $p += strlen($ret);
        return $ret;
    }

    /**
     * Write to the content.
     *
     * @param string $data The data to add to the stream.
     *
     * @return int
     */
    public function stream_write(string $data): int
    {
        $v =& $this->content;
        $l = strlen($data);
        $p =& $this->position;
        $v = substr($v, 0, $p) . $data . substr($v, $p += $l);
        return $l;
    }

    /**
     * Return the current position
     * in the stream.
     *
     * @return int
     */
    public function stream_tell(): int
    {
        return $this->position;
    }

    /**
     * Tests for end-of-stream on a pointer position.
     *
     * @return bool
     */
    public function stream_eof(): bool
    {
        return ($this->position >= strlen($this->content));
    }

    /**
     * Move the cursor inside the stream to this position.
     *
     * @param int $offset Seek this position in the stream.
     * @param int $whence The seek type.
     *
     * @see https://www.php.net/fseek for more information.
     *
     * @return bool
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        $l = strlen($this->content);
        $p =& $this->position;
        switch ($whence) {
            case SEEK_SET:
                $newPos = $offset;
                break;
            case SEEK_CUR:
                $newPos = $p + $offset;
                break;
            case SEEK_END:
                $newPos = $l + $offset;
                break;
            default:
                return false;
        }
        $ret = ($newPos >= 0 && $newPos <= $l);
        if ($ret) $p = $newPos;
        return $ret;
    }
}
