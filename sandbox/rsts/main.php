<?php
/*
 * This file is part of RSTS
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define('TESTS_DIR', realpath(__DIR__ . '.\tests\Cortex'));

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/app/FileIO.php');

use App\TestCase;
use Symfony\Component\Yaml\Yaml;
use function App\getTests;

$tests = getTests(TESTS_DIR);
sort($tests);

$stats = [
    'success' => 0,
    'failed'   => 0,
    'asserts' => 0,
];

$time_start = microtime(true);

/**
 * For all files in the tests directory
 * run its tests.
 */
foreach ($tests as $filename) {
    $files = Yaml::parseFile($filename);
    $basename = basename($filename);
    $relativeFile = substr($filename, strlen(TESTS_DIR) +1);

    echo "{$relativeFile}\n";

    foreach ($files as $name => $cases) {
        $test = new TestCase($basename, $name, $cases);
        $result = $test->run();

        $stats['success'] += $result['success'];
        $stats['failed'] += $result['failed'];
        $stats['asserts'] += $result['asserts'];
    }

    echo "\n";
}

// Display Script End time
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

echo <<<EOF
Tests:  {$stats['failed']} failed, {$stats['success']} passed
Time:   {$execution_time}s
EOF;

if ($stats['failed'] > 0) {
    exit(1);
}

