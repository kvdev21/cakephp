<?php
namespace Com\Kaleidovision\KL4;

class AutoUpdateFolders {
    public $configIndex = -15;
    public $coreConfigDir = '';
    public $bsmConfigDir = '';
    public $upsConfigDir = '';
    public $musicConfigDir = '';

    public function __construct() {
        $bFound = false;
        if (file_exists('C:' . DS . 'Kaleidovision' . DS . 'SharedLocal' . DS . 'CurrentConfigIndex.ini')) {
            $aCurrentConfig = parse_ini_file('C:' . DS . 'Kaleidovision' . DS . 'SharedLocal' . DS . 'CurrentConfigIndex.ini');
            if ($aCurrentConfig['ConfigIndex'] > 0) {
                $this->configIndex = $aCurrentConfig['ConfigIndex'];
                $bFound = true;
            }
        }

        if ($bFound === false) {
            $e = dir('C:' . DS . 'Kaleidovision' . DS . 'systems');
            while (false !== ($entry = $e->read())) {
                if (is_numeric($entry)) {
                    $path = 'C:' . DS . 'Kaleidovision' . DS . 'systems' . DS . $entry . DS . 'config' . DS . 'kv';
                    if (is_dir($path)) {
                        $latestDate = $this->findLatestDatedFolder($path);
                        if (file_exists('C:' . DS . 'kaleidovision' . DS . 'systems' . DS . $entry . DS . 'config' . DS . 'kv' . DS . $latestDate . DS . 'VenueInfo.cfg')) {
                            $this->configIndex = $entry;
                            break;
                        }
                    }
                }
            }
        }

        $latestDate = $this->findLatestDatedFolder('C:' . DS . 'kaleidovision' . DS . 'systems' . DS . '' . $this->configIndex . '' . DS . 'config' . DS . 'kv');
        if ($latestDate > '')
            $this->coreConfigDir = 'C:' . DS . 'kaleidovision' . DS . 'systems' . DS . '' . $this->configIndex . '' . DS . 'config' . DS . 'kv' . DS . '' . $latestDate;

        $latestDate = $this->findLatestDatedFolder('C:' . DS . 'kaleidovision' . DS . 'systems' . DS . '' . $this->configIndex . '' . DS . 'config' . DS . 'bsm');
        if ($latestDate > '')
            $this->bsmConfigDir = $latestDate;

        $latestDate = $this->findLatestDatedFolder('C:' . DS . 'kaleidovision' . DS . 'systems' . DS . '' . $this->configIndex . '' . DS . 'config' . DS . 'bsm');
        if ($latestDate > '')
            $this->upsConfigDir = $latestDate;

        $latestDate = $this->findLatestDatedFolder('C:' . DS . 'kaleidovision' . DS . 'systems' . DS . '' . $this->configIndex . '' . DS . 'music' . DS . 'channel1');
        if ($latestDate > '')
            $this->musicConfigDir = $latestDate;
    }

    public function findLatestDatedFolder($path) {
        $currentDir = '';
        $d = dir($path);
        while (false !== ($entry = $d->read())) {
            if (($entry > $currentDir) and $this->isValidDatedFolder($entry))
                $currentDir = $entry;
        }
        return $currentDir;
    }

    public function isValidDatedFolder($folder) {
        if (strlen($folder) <> 15)
            return false;
        if (!mktime(substr($folder, 11, 2), substr($folder, 13, 2), 0, substr($folder, 5, 2), substr($folder, 8, 2), substr($folder, 0, 4)))
            return false;
        return true;
    }
}