<?php
namespace Vulcan\Rivescript;

use SplFileObject;

class Parser extends Utility;
{
    /**
     * Process Rivescript file.
     *
     * @param string  $file
     */
    public function process($file)
    {
        $tree = [
            'begin' => [
                'global' => [],
                'var'    => [],
                'sub'    => [],
                'person' => [],
                'array'  => []
            ],
            'topics'  => [],
            'objects' => []
        ];

        $file       = new SplFileObject($file);
        $lineNumber = 0;

        while (! $file->eof()) {
            $lineNumber++;
            $line = $this->removeWhitespace($file->fgets());

            // Skip empty lines
            if (empty($line)) continue;

            // Parse comments
            if ($this->startsWith($line, '//')) {
                continue;
            } elseif ($this->startsWith($line, '#')) {
                $this->warning('Using the # symbol for comments is deprecated');
                continue;
            } elseif ($this->startsWith($line, '/*')) {
                if ($this->endsWith($line, '*/')) continue;

                $insideComment = true;
                continue;
            } elseif ($this->endsWith($line, '*/')) {
                $insideComment = false;
                continue;
            }

            if ($insideComment === true) continue;
        }

        $file = null;

        return $tree;
    }

    /**
     * Parse comments.
     *
     * @return mixed
     */
    protected function parseComments($line)
    {

    }

    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param string  $line
     * @return string
     */
    public function removeWhitespace($line)
    {
        $line = trim($line);
        preg_replace('/( )+/', ' ', $line);

        return $line;
    }
}
