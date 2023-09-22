<?php
/**
 * @package Com.Kaleidovision.Shell
 * @subpackage Com.Kaleidovision.Shell.FfmpegProgram
 */

namespace Com\Kaleidovision\Shell;

class WaveformProgram extends ShellProgram {
    private $binPath;
    private $options;
    private $pngfile;
    private $audiofile;

    /**
     * __construct
     *
     * @param  string $binLocation
     * @return WaveformProgram
     */
    public function __construct($binLocation) {
        parent::__construct($binLocation);

        return $this;
    }

    /**
     * run
     *
     * @param  string $audiofile
     * @param  string $pngfile
     * @return void
     */
    public function run($audiofile, $pngfile)
    {
        $this->audiofile = $audiofile;
        $this->pngfile = $pngfile;
        $result = $this->process();
        if (!$result) {
            throw new \RuntimeException('WaveformProgram: exec failed');
        }
    }

    /**
     * setOption
     *
     * @param  string            $key
     * @param  string            $value
     * @return WaveformProgram
     */
    protected function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * setWidth
     *
     * @param  int               $width
     * @return WaveformProgram
     */
    public function setWidth($width)
    {
        $this->setOption('width', $width);

        return $this;
    }

    /**
     * setHeight
     *
     * @param  mixed             $height
     * @return WaveformProgram
     */
    public function setHeight($height)
    {
        $this->setOption('height', $height);

        return $this;
    }

    /**
     * setColorBg
     *
     * @param  string            $color
     * @param  int               $alpha
     * @return WaveformProgram
     */
    public function setColorBg($color, $alpha = 1)
    {
        $this->setOption('color-bg', $color . self::convertAlphaToHex($alpha));

        return $this;
    }

    /**
     * setColorCenter
     *
     * @param  string            $color
     * @param  int               $alpha
     * @return WaveformProgram
     */
    public function setColorCenter($color, $alpha = 1)
    {
        $this->setOption('color-center', $color . self::convertAlphaToHex($alpha));

        return $this;
    }

    /**
     * setColorOuter
     *
     * @param  string            $color
     * @param  int               $alpha
     * @return WaveformProgram
     */
    public function setColorOuter($color, $alpha = 1)
    {
        $this->setOption('color-outer', $color . self::convertAlphaToHex($alpha));

        return $this;
    }

    /**
     * convertAlphaToHex
     *
     * @param  int    $alpha
     * @return string
     */
    public static function convertAlphaToHex($alpha)
    {
        return sprintf("%02X", round($alpha * 255));
    }

    protected function process()
    {
        $this->arguments = sprintf('"%s" "%s"', $this->audiofile, $this->pngfile);
        if(!empty($this->options)) {
            foreach ($this->options as $key => $value) {
                $this->arguments .= sprintf(' --%s %s', $key, $value);
            }
        }

        return $this->runCommand();
    }
}