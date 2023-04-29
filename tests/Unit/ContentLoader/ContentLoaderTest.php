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

use Axiom\Rivescript\ContentLoader\ContentLoader;

beforeEach(function () {
    $this->loader = new ContentLoader();
});

test(
    'getStream loader should work',
    function () {
        $this->assertIsResource($this->loader->getStream());
    }
);

test(
    'load() can load a single file',
    function () {
        $expected = "THIS IS THE CONTENT";

        $this->loader->load(__DIR__ . '/../../resources/contentloader/content.txt');
        $stream = $this->loader->getStream();

        if (is_resource($stream)) {
            rewind($stream);

            $actual = fgets($stream);
            $this->assertEquals($expected, $actual);
        } else {
            $this->markTestSkipped("Could not load file content.txt");
        }
    }
);

test(
    'load() can load a multiple file passed as an array.',
    function () {
        $files = [
            __DIR__ . '/../../resources/contentloader/files/file1.rive',
            __DIR__ . '/../../resources/contentloader/files/file2.rive',
        ];

        $expected = [
            '+ bleep1',
            '- bleep1 response',
            '+ bleep2',
            '- bleep2 response',
        ];

        $actual = [];

        $this->loader->load($files);
        $stream = $this->loader->getStream();

        if (is_resource($stream)) {
            rewind($stream);

            while (!feof($stream)) {
                $actual[] = trim(fgets($stream));
            }

            $this->assertEquals($expected, $actual);
        } else {
            $this->markTestSkipped("Could not load the array of files.");
        }
    }
);

test(
    'load() can load files in a given directory passed as string',
    function () {
        $expected = [
            '+ here we go',
            '- yes there we go',
            '+ *',
            '- directory test confused'
        ];

        $actual = [];

        $this->loader->load(__DIR__ . '/../../resources/contentloader/directory');
        $stream = $this->loader->getStream();

        if (is_resource($stream)) {
            rewind($stream);

            while (!feof($stream)) {
                $actual[] = trim(fgets($stream));
            }

            $this->assertEquals($expected, $actual);
        } else {
            $this->markTestSkipped("Could not load the array of files.");
        }
    }
);