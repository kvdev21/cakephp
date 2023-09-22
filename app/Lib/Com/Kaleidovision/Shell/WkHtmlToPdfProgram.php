<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.WkHtmlToPdfProgram
 */

namespace Com\Kaleidovision\Shell;

class WkHtmlToPdfProgram extends Shellprogram {
    public function __construct($binLocation) {
        parent::__construct($binLocation);

        $this->lowPriority = false;
    }

    public function convert($inputFile, $outputFile) {
        $this->arguments = $inputFile . ' -t --footer-right "Page [page]/[toPage]" ' . $outputFile;

        return $this->runCommand();
    }
}