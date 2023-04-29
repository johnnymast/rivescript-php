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

/**
 * ContentStream class
 *
 * This class is a helper class for reading from and writing to
 * io streams.
 *
 * Note: This class taken from php.net and changed to
 *       be workable for this project.
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
class ContentStream
{
    /**
     * The cursor position inside the stream.
     *
     * @var int
     */
    private int $position = 0;

    /**
     * The variable that holds
     * the contents of the stream.
     *
     * @var string
     */
    protected string $content = '';

    /**
     * Specifies the URL that was passed to the original function.
     * This is currently not used.
     *
     * @var string
     */
    protected string $path = '';

    /**
     * The mode to operate in.
     * This is currently not used.
     *
     * @var string
     */
    protected string $mode = '';

    /**
     * (Re)set default values when opening a stream.
     *
     * @see https://www.php.net/manual/en/function.fopen.php
     *
     * @param string $mode The mode used to open the file, as detailed for fopen().
     * @param string $path Specifies the URL that was passed to the original function.
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
     * Support for fstat().
     *
     *   An array with file status, or FALSE in case of an error - see fstat()
     *   for a description of this array.
     *
     * @see http://php.net/manual/streamwrapper.stream-stat.php
     *
     * @return array
     */
    public function stream_stat(): array
    {
        return (array)$this->content;
    }

    /**
     * Move the cursor inside the stream to this position.
     *
     * @see https://www.php.net/fseek for more information.
     *
     * @param int $whence The seek type.
     * @param int $offset Seek this position in the stream.
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
        if ($ret) {
            $p = $newPos;
        }
        return $ret;
    }
}
