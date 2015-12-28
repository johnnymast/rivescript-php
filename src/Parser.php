<?php
namespace Vulcan\Rivescript;

use SplFileObject;

class Parser
{
    /**
     * Process Rivescript file.
     *
     * @param string  $file
     */
    public function process($file)
    {
        $file       = new SplFileObject($file);
        $lineNumber = 0;

        while (! $file->eof()) {
            $lineNumber++;
            $line = $this->removeWhitespace($file->fgets());

            if (empty($line)) continue;

            echo $line;
        }

        $file = null;
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
