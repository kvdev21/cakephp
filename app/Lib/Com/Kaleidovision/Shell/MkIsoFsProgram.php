<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.MkIsoFsProgram
 */

namespace Com\Kaleidovision\Shell;

class MkIsoFsProgram extends ShellProgram {
    public $inputDirectory;
    public $outputFile;

    public function __construct($binLocation) {
        parent::__construct($binLocation);
    }

    public function dvdDirectoryToIso() {
        $this->arguments = ' -dvd-video -o "' . $this->outputFile . '"';
        $this->arguments .= ' "' . $this->inputDirectory . '"';

        return $this->runCommand();
    }
}