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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Get a list of test files.
 *
 * @param string $path The directory to read.
 *
 * @return array|void
 */
function getTests(string $path): array
{
    $files = [];
    //  $iterator = new RecursiveDirectoryIterator($path) or die(__FUNCTION__ . ": Failed opening directory {$path} for reading");
    $iterator = new TestCaseFilter(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))) or
    die(__FUNCTION__ . ": Failed opening directory {$path} for reading");


    foreach ($iterator as $file) {
        if ($file->isDir() === true) {
            continue;
        }

        // Fout
        //
        // begin.yml
        // replies.yml
        // triggers.yml
        // test-spec.yml

        if ($file->getBasename() !== 'replies.yml') {
        //        continue;
        }


        $files[] = $file->getPathname();
    }

    return $files;
}
