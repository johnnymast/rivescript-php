<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

/**
 * Wildcard class
 *
 * Description:
 *
 * This class contains information about aa detected wildcard.
 *
 * Using an asterisk (*) in the trigger will make it act as a wildcard. Anything the user says in place of the wildcard
 * may still match the trigger. For example:
 *
 * + my name is *
 * - Pleased to meet you, <star>.
 * An asterisk (*) will match any character (numbers and letters). If you want to only match numbers, use #, and to
 * match only letters use _. Example:
 *
 * // This will ONLY take a number as the wildcard.
 * + i am # years old
 * - I will remember that you are <star> years old.
 *
 * // This will ONLY take letters but not numbers.
 * + my name is _
 * - Nice to meet you, <star>.
 * The values matched by the wildcards can be retrieved in the responses by using the tags <star1>, <star2>, <star3>,
 * etc. in the order that the wildcard appeared. <star> is an alias for <star1>.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Trigger-Wildcards
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Wildcard
{
    /**
     * Array with wildcard
     * symbols.
     *
     * @var array<string, array>
     */
    public static array $types = [
        '_' => [
            'type' => 'letter',
            'search_regex' => '/_/',
            'replace_regex' => '[^\s\d]+?',
            'number' => 0
        ],
        '#' => [
            'type' => 'letter',
            'search_regex' => '/#/',
            'replace_regex' => '\\d+?',
            'number' => 0
        ],
        '*' => [
            'type' => 'wildcard',
            'search_regex' => '/\*/',
            'replace_regex' => '.*?',
            'number' => 0
        ],
    ];
    /**
     * The tag this wildcard.
     *
     * @var string
     */
    protected string $tag = '';

    /**
     * @param string $character
     * @param string $type
     * @param int    $stringPosition
     * @param int    $order
     */
    public function __construct(
        protected string $character = '',
        protected string $type = '',
        protected int    $stringPosition = 0,
        protected int    $order = 0
    )
    {
    }

    /**
     * Return the available Wildcard types.
     *
     * @return array|array[]|string[]
     */
    public static function getAvailableTyppes(): array
    {
        return self::$types;
    }

    /**
     * Return the character of wildcard.
     *
     * @return string
     */
    public function getCharacter(): string
    {
        return $this->character;
    }

    /**
     * Return the type of wildcard.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the wildcard position.
     *
     * @return int
     */
    public function getStringPosition(): int
    {
        return $this->stringPosition;
    }

    /**
     * Return the search regex for this wildcard.
     *
     * @return string
     */
    public function getSearchRegex(): string
    {
        if (isset(self::$types[$this->character])) {
            return self::$types[$this->character]['search_regex'];
        }

        return false;
    }

    /**
     * Return the replace regex for this wildcard.
     *
     * @return string
     */
    public function getReplaceRegex(): string
    {
        if (isset(self::$types[$this->character])) {
            return self::$types[$this->character]['replace_regex'];
        }

        return false;
    }

    /**
     * Return the tag fore this wildcard.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Set the order of in wich the wildcard has been found.
     *
     * @param int $order The sort order for this wildcard.
     *
     * @return void
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    /**
     * Set the tag for this wildcard.
     * for example :wildcard0.
     *
     * @param string $tag The tag to set.
     *
     * @return void
     */
    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type . $this->order;
    }
}