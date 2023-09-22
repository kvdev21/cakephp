<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.GrowIsoFsProgram
 */

namespace Com\Kaleidovision\Shell;

class GrowIsoFsProgram extends ShellProgram {
    public $inputFile;
    public $drive;

    public function __construct($binLocation) {
        parent::__construct($binLocation);
    }

    public function burnIso() {
        $this->arguments = ' -dvd-compat -dvd-video -Z ' . $this->drive . '="' . $this->inputFile . '"';

        if($this->runCommand()) {
            // Search for errors in growisofs output
            if(!empty($this->output) && is_array($this->output)) {
                foreach($this->output as $line) {
                    if(substr($line, 0, 3) == ':-[') return false;
                    if(substr($line, 0, 3) == ':-(') return false;
                    if(strpos($line, 'already carries isofs!') != false) return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }
}