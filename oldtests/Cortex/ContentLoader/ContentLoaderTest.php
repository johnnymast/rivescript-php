<?php

/**
 * Tests the loading of content into the interpreter.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     ContentLoader
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\ContentLoader;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
    })
    ->group('contentloader');


it('can load interoperable information from text stream.', function () {
    $content = <<<EOF
+ how are you
- I am fine thanks for asking.
EOF;

    $this->rivescript->stream($content);

    $expected = "I am fine thanks for asking.";
    $actual = $this->rivescript->reply("how are you");

    $this->assertEquals($expected, $actual);
});

it('can load interoperable information from a directory.', function () {


    $this->rivescript->load(__DIR__ . '/../../resources/contentloader/directory');

    // directory/file1.rive
    $expected = "yes there we go";
    $actual = $this->rivescript->reply("here we go");
    $this->assertEquals($expected, $actual);

    // directory/file2.rive
    $expected = "directory test confused";
    $actual = $this->rivescript->reply("Hello world");

    $this->assertEquals($expected, $actual);
});

it('can load interoperable multiple files passed as array.', function () {


    $this->rivescript->load([
        __DIR__ . '/../../resources/contentloader/files/file1.rive',
        __DIR__ . '/../../resources/contentloader/files/file2.rive'
    ]);

    // files/file1.rive
    $expected = "bleep1 response";
    $actual = $this->rivescript->reply("bleep1");
    $this->assertEquals($expected, $actual);

    // files/file2.rive
    $expected = "bleep2 response";
    $actual = $this->rivescript->reply("bleep2");
    $this->assertEquals($expected, $actual);
});

it('can load a interoperable single file passed as string.', function () {
    $this->rivescript->load(__DIR__ . '/../../resources/contentloader/content.rive',);

    // content.rive
    $expected = "erm bot generated content";
    $actual = $this->rivescript->reply("now this is content");
    $this->assertEquals($expected, $actual);
});



it('can load a interoperable multiple files passed as array.', function () {
    $this->rivescript->load(__DIR__ . '/../../resources/contentloader/content.rive',);

    // content.rive
    $expected = "erm bot generated content";
    $actual = $this->rivescript->reply("now this is content");
    $this->assertEquals($expected, $actual);
});

