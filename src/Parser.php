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

        $file          = new SplFileObject($file);
        $lineNumber    = 0;
        $topic         = 'random';
        $insideComment = false;

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

            // Separate command from the data
            if (strlen($line) < 2) {
                $this->warning("Weird single-character line #$linenumber found.");
                continue;
            }

            $command = substr($line, 0, 1);
            $line    = $this->removeWhitespace(substr($line, 1));

            // Sort out the types of Rivescript commands
            switch ($command) {
                // Definition
                case '!':
                    switch ($type) {
                        case 'local':
                            break;

                        case 'global':
                            break;

                        case 'var':
                            break;

                        case 'array':
                            break;

                        case 'sub':
                            break;

                        case 'person':
                            break;

                        default:
                            $this->warning("Unknown definition type '$type'");
                    }
                    break;

                // Start of Labeled Section
                case '>':
                    break;

                // End of Labeled Section
                case '<':
                    break;

                // Trigger
                case '+':
                    break;

                // Response
                case '-':
                    break;

                // Condition
                case '*':
                    break;

                // Previous
                case '%':
                    continue;
                    break;

                // Continue
                case '^':
                    continue;
                    break;

                // Redirect
                case '@':
                    break;

                default:
                    $this->warning("Unknown command '$command'.");
            }
        }

        $file = null;

        return $tree;
    }
}
