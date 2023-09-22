<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.NirCmdProgram
 */

namespace Com\Kaleidovision\Shell;

class NirCmdProgram extends Shellprogram {
    public function __construct($binLocation) {
        parent::__construct($binLocation);
        $this->lowPriority = false;
    }

    public function cdTray($drive, $openClose) {
        $this->arguments = ' cdrom';

        if($openClose == 'open') {
            $this->arguments .= ' open ';
        } else {
            $this->arguments .= ' close ';
        }

        $this->arguments .= $drive;

        return $this->runCommand();
    }
}