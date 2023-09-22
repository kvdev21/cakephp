<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.FfmpegProgram
 */

namespace Com\Kaleidovision\Shell;

class FfmpegProgram extends ShellProgram {
    public $inputFile;
    public $outputFile;
    public $aspectRatio;
    public $targetFormat;

    public function __construct($binLocation) {
        parent::__construct($binLocation);

        $this->aspectRatio = '16:9';
        $this->targetFormat = 'pal-dvd';
    }

    public function mpeg2() {
        $this->arguments = ' -i "' . $this->inputFile . '"';
        $this->arguments .= ' -sameq -vcodec mpeg2video -acodec mp2 -ab 192k -ar 48000 -aspect ' . $this->aspectRatio;
        if($this->targetFormat == 'ntsc-dvd') {
            $this->arguments .= ' -vf "scale=720:480" -r 29.97';
        } else {
            $this->arguments .= ' -vf "scale=720:576" -r 25';
        }
        $this->arguments .= ' "' . $this->outputFile . '"';

        return $this->runCommand();
    }

    public function dvdMpegFormat() {
        $this->arguments = ' -i "' . $this->inputFile . '"';
        $this->arguments .= ' -aspect ' . $this->aspectRatio;
        $this->arguments .= ' -target ' . $this->targetFormat;
        $this->arguments .= ' -ab 192k "' . $this->outputFile . '"';

        return $this->runCommand();
    }

    public function dvdMpegFormatSameQuality() {
        $this->arguments = ' -i "' . $this->inputFile . '"';
        $this->arguments .= ' -target ' . $this->targetFormat;
        $this->arguments .= ' -acodec ac3 -ab 192k -ar 48000 -aspect ' . $this->aspectRatio;

        if($this->targetFormat == 'ntsc-dvd') {
            // Re-scale & change frame rate for NTSC
            $this->arguments .= ' -vf "scale=720:480" -r 29.97';
        } else {
            $this->arguments .= ' -vcodec copy -sameq';
        }

        $this->arguments .=  ' "' . $this->outputFile . '"';

        return $this->runCommand();
    }

    public function extractWav() {
        $this->arguments = ' -y -i "' . $this->inputFile . '"';
        $this->arguments .= ' -acodec pcm_s16le';
        $this->arguments .= ' -ac 2 "' . $this->outputFile . '"';

        return $this->runCommand();
    }
}