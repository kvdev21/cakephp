<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.Shell
 */

namespace Com\Kaleidovision\Shell;

class Shell {
    protected $command;
    private $redirectStderr;
    private $priority;

    public $output;
    public $return;
    public $debug;

    public function __construct() {
        #$this->redirectStderr = true;
        $this->redirectStderr = false;
        $this->priority = 'NORMAL';
        $this->debug = false;
    }

    public function setCommand($command) {
        $this->command = (string) $command;
    }

    public function runCommand() {
        if($this->redirectStderr)
            $command = $this->command . ' 2>&1'; // Redirect stderr to stdout
        else
            $command = $this->command;

        $this->output = array(); // Empty output

        echo ($this->debug) ? '<hr />RUNNING COMMAND: ' . $command . '<br />' : '';
        exec($command, $this->output, $this->return);
        echo ($this->debug) ? nl2br($this->getOutputString()) : '';
        if($this->return === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getCommand() {
        return (string) $this->command;
    }

    public function getOutputString() {
        if(!empty($this->output)) {
            $outputString = '';
            foreach($this->output as $outputLine) $outputString .= "$outputLine\n";
        } else {
            $outputString = 'No output.';
        }

        return $outputString;
    }
}