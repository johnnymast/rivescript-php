<?php
/*
 * This file is part of RSTS
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Rsts;

use Axiom\Rivescript\Rivescript;
use AssertionError;

/**
 * TestCase trait
 *
 * The TestCase class will test a single file
 * with tests.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Tests
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class TestCase
{

    /**
     * The file name.
     *
     * @var string
     */
    protected string $file;

    /**
     * The suite name.
     *
     * @var string
     */
    protected string $name;

    /**
     * Instance of the rivescript interpreter.
     *
     * @var \Axiom\Rivescript\Rivescript
     */
    protected Rivescript $rs;

    /**
     * The test client id.
     *
     * @var string
     */
    protected string $client_id = "test-user";

    /**
     * Options added for this test
     * case.
     *
     * @see https://github.com/aichaos/rsts/blob/master/tests/test-spec.yml#L3
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Stores the cases for this test.
     *
     * @var mixed
     */
    private array $cases;

    /**
     * TestCase constructor.
     *
     * @param string $file    The test file to load.
     * @param string $name    The name of the test.
     * @param array  $options The testcases options.
     */
    public function __construct(string $file, string $name, array $options)
    {
        $this->rs = new Rivescript();

        $this->options = $options;

        $this->rs->onSay = function ($msg) {
            //   echo "{$msg}\n";
        };


        $this->rs->onWarn = function ($msg) {
            //    echo "{$msg}\n";
        };
        $this->file = $file;
        $this->name = ucfirst(str_replace("_", " ", $name));

        $this->cases = $this->options['tests'];

        unset($this->options['tests']);

        $this->handleOptions();

    }

    /**
     * If this test case has defined options
     * for the interpreter set them.
     *
     * @return void
     */
    private function handleOptions(): void
    {
        foreach ($this->options as $key => $val) {
            switch ($key) {
                case 'username':
                    $this->client_id = $val;
                    break;
                case 'utf8':
                    $this->rs->utf8($val);
                    break;
            }
        }

        $this->rs->setClientId($this->client_id);
    }

    /**
     * Apply the test source code.
     *
     * @param string $source The script source.
     *
     * @return void
     */
    private function source(string $source): void
    {
        $this->rs->stream($source);
    }

    /**
     * Input tests an input string against the interpreter.
     *
     * @param array $step The input step to test.
     *
     * @return void
     */
    private function input(array $step): void
    {

        $reply = $this->rs->reply($step['input'], $this->client_id);

        if (!isset($step['reply'])) {
            return;
        }

        $expected = $step['reply'];

        if (is_string($expected) === true) {
            $expected = rtrim($expected, "\n");
            if (strtolower($reply) !== strtolower($expected)) {
                throw new AssertionError(
                    "Got unexpected exception from reply() for input: {$step['input']}\n\n" .
                    " Expected: {$expected}\n" .
                    " Got: {$reply}"
                );
            }
        } elseif (is_array($expected) === true) {
            $correct = 0;
            foreach ($expected as $item) {
                $item = rtrim($item, "\n");
                if (strtolower($reply) === strtolower($item)) {
                    $correct++;
                }
            }

            if ($correct === 0) {
                $expected = implode(' or ', $expected);
                throw new AssertionError(
                    "Got unexpected exception from reply() for input: {$step['input']}\n\n" .
                    " Expected: {$expected}\n" .
                    " Got: {$reply}"
                );
            }
        }
    }

    /**
     * Ser a user variable.
     *
     * @param array $vars The variables to set.
     *
     * @return void
     */
    public function set(array $vars): void
    {
        if (count($vars) > 0) {
            foreach ($vars as $key => $value) {
                $this->rs->set_uservar($key, $value);
            }
        }
    }

    /**
     * Assert the value of a user variable.
     *
     * @param array $vars The variables to test.
     *
     * @return void
     */
    private function assert(array $vars): void
    {
        foreach ($vars as $key => $value) {
            $expected = $value;
            $actual = $this->rs->get_uservar($key);

            if ($actual != $value) {
                throw new AssertionError(
                    "Failed to assert that the value of user variable: {$key}\n\n" .
                    " Expected: {$expected}\n" .
                    " Got: {$actual}"
                );
            }
        }
    }

    /**
     * Tun the tests in this suite.
     *
     * @return array
     */
    public function run(): array
    {
        $stats = [
            'success' => 0,
            'failed' => 0,
            'asserts' => 0,
        ];

        try {

            foreach ($this->cases as $step) {
                $key = key($step);

                switch ($key) {
                    case "assert":
                        $this->assert($step[$key]);
                        $stats['asserts']++;
                        break;
                    case "source":
                        $this->source($step[$key]);
                        break;

                    case "input":
                        $this->input($step);
                        $stats['success']++;
                        break;

                    case "set":
                        $this->set($step[$key]);
                        break;

                    default:
                        throw new AssertionError("Unsupported test step called \"{$key}\"");
                }
            }

            $sym = "✓";
            $name = ucfirst(str_replace("_", " ", $this->name));
            echo " {$sym} {$name}\n";
        } catch (AssertionError $e) {
            $stats['failed']++;
            $this->fail($e);
        }

        return $stats;
    }

    /**
     * Show a failure message.
     *
     * @param \AssertionError $e The fail exception.
     *
     * @return void
     */
    private function fail(AssertionError $e): void
    {
        $banner = "x {$this->name}";
        $banner .= "\n " . str_repeat("=", strlen($banner)) . "\n";

        echo " {$banner} {$e->getMessage()} \n\n";
    }
}
