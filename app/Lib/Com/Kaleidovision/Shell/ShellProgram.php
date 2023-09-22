<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.ShellProgram
 */

namespace Com\Kaleidovision\Shell;

class ShellProgram extends Shell {
    protected $target;
    public $arguments;

    public function __construct($binLocation) {
        parent::__construct();

        $this->target = $binLocation;

        $this->priority = 'NORMAL';
    }

    public function runCommand() {
        switch($this->priority) {
            case 'LOW':
                $this->command = __DIR__ . '/bin/low_priority.bat ' . $this->target . ' ' . $this->arguments;
                break;
            case 'BELOWNORMAL':
                $this->command = __DIR__ . '/bin/belownormal_priority.bat ' . $this->target . ' ' . $this->arguments;
                break;
            default:
                $this->command = $this->target . ' ' . $this->arguments;
                break;
        }

        if($this->priority != 'NORMAL') {
            parent::runCommand();
            if(!in_array('#KV_ERROR_LEVEL: 0', $this->output)) {
                return false;
            } else {
                return true;
            }
        } else {
            return parent::runCommand();
        }
    }
}