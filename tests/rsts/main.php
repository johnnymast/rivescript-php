<?php
/*
 * This file is part of RSTS
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define('TESTS_DIR', realpath(__DIR__ . '.\tests'));

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/src/FileIO.php');

use Tests\Rsts\TestCase;
use Symfony\Component\Yaml\Yaml;
use function Tests\Rsts\getTests;

$stats = [
    'success' => 0,
    'failed' => 0,
    'asserts' => 0,
];

$time_start = microtime(true);

/**
 * Please note: It is possible you don't see any files
 * in official/ in that case you need to run the following
 * command in the rivescript-php project root.
 *
 * git submodule update --init
 */
$paths = [
//    realpath(__DIR__ . '/tests'),
realpath(__DIR__ . '\official\tests'),
];


foreach ($paths as $path) {
    $tests = getTests($path);
    sort($tests);

    /**
     * For all the test suites and
     * run its tests.
     */
    foreach ($tests as $filename) {
        $files = Yaml::parseFile($filename);
        $basename = basename($filename);
        $relativeFile = substr($filename, strlen($path) + 1);

        echo "{$relativeFile}\n";

        foreach ($files as $name => $options) {
            $test = new TestCase($basename, $name, $options);
            $result = $test->run();

            $stats['success'] += $result['success'];
            $stats['failed'] += $result['failed'];
            $stats['asserts'] += $result['asserts'];
        }

        echo "\n";
    }
}


$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

echo <<<EOF
Tests:  {$stats['failed']} failed, {$stats['success']} passed
Time:   {$execution_time}s
EOF;

if ($stats['failed'] > 0) {
    exit(1);
}
