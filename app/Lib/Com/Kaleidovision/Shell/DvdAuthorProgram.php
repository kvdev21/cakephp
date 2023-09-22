<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.DvdAuthorProgram
 */

namespace Com\Kaleidovision\Shell;

class DvdAuthorProgram extends ShellProgram {
    public $inputFile;
    public $outputDirectory;

    public $chapters;
    public $xmlFile;

    public function __construct($binLocation) {
        parent::__construct($binLocation);

        $this->chapters = array();
    }

    public function mpegToDvdDirectory() {
        // Convert MPEG to VOBs and create directories
        $this->arguments = ' -o "' . $this->outputDirectory . '"';
        $this->arguments .= ' -t "' . $this->inputFile . '"';

        if($this->runCommand()) {
            // Create VIDEO_TS files
            $this->arguments = ' -T -o "' . $this->outputDirectory . '"';

            return $this->runCommand();
        } else {
            return false;
        }
    }

    public function addChapter($chapter) {
        $this->chapters[] = $chapter;
    }

    public function multiChapterDvdDirectory() {
        if(!empty($this->chapters)) {
            // Generate the XML needed
            $xml = '
                <dvdauthor>
                    <vmgm />
                    <titleset>
                        <titles>
                            <pgc>
            ';

            foreach($this->chapters as $chapter) {
                $xml .= "\n                   <vob file=\"{$chapter}\" />\n";
            }

            $xml .= '
                            </pgc>
                        </titles>
                    </titleset>
                </dvdauthor>
            ';

            $fh = fopen($this->xmlFile, 'w');
            fwrite($fh, $xml);
            fclose($fh);

            // Run the program
            $this->arguments = ' -o "' . $this->outputDirectory . '"';
            $this->arguments .= ' -x "' . $this->xmlFile . '"';

            return $this->runCommand();
        } else {
            return false;
        }
    }
}