<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.WgetProgram
 */

namespace Com\Kaleidovision\Shell;

class WgetProgram extends Shellprogram {
    public function __construct($binLocation) {
        parent::__construct($binLocation);

        $this->lowPriority = false;
    }

    public function get($url, $outputFile) {
        $this->arguments = $url . ' > ' . $outputFile;

        return $this->runCommand();
    }
}