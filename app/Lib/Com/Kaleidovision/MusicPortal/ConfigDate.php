<?php
/**
 * This file contains classes which allow tracks to be
 * encrypted and decrypted
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.ConfigDate
 */

namespace Com\Kaleidovision\MusicPortal;

// Load configuration
require_once('Configuration.php');

require_once('Data.php');
require_once('Playlist.php');

class ConfigDate extends Data  {
    public $id = 0;
    public $tracks;
    public $profiles;
    public $title = '';
    public $date = '0000-00-00 00:00:00';
    public $channel = '';
    public $zipLocation = '';
    public $entityId = 0;
    public $companyMusicEntityId = 0;
    public $excludeTrackUids;
    public $isLatest = false;
    private $checkTrackExclusions = false;

    protected function _exportArray() {
        $this->_data = array(
            'MusicEntityConfigDate' => array(
                'title' => $this->title,
                'entity_id' => $this->entityId,
                'company_music_entity_id' => $this->companyMusicEntityId,
                'date' => $this->date,
                'channel' => $this->channel
            )
        );

        if(!empty($this->id)) $this->_data['Track']['id'] = $this->id;

        if(!empty($this->tracks)) {
            $i = 0;
            foreach($this->tracks as $track) {
                $trackData = $track->save();
                $this->_data['Track'][$i] = $trackData['Track'];
                $i++;
            }
        }

        parent::_exportArray();
    }

    public function getConfigDateFromFilename($filename = '') {
        $filename = (empty($filename)) ? $this->title : $filename;

        $filenameParts = explode('.', $filename);
        if(!empty($filenameParts)) {
            if(!empty($filenameParts[3])) {
                $dateParts = explode('-', $filenameParts[3]);
                if(!empty($dateParts)) {
                    $date = $dateParts[0] . '-' . $dateParts[1] . '-' . $dateParts[2];
                    $date .= ' ';
                    $date .= substr($dateParts[3], 0, 2);
                    $date .= ':';
                    $date .= substr($dateParts[3], 2, 2);
                    return $date;
                }
            }
        }

        return false;
    }

    public function loadFromZip($zipLocation = '', $excludeTrackUids = null) {
        if(!empty($zipLocation)) $this->zipLocation = $zipLocation;
        if(!empty($excludeTrackUids)) $this->excludeTrackUids = $excludeTrackUids;

        if(!empty($this->excludeTrackUids)) {
            $this->checkTrackExclusions = true;
        }

        $zip = new \ZipArchive();

        if($zip->open($this->zipLocation)) {
            $this->title = basename($this->zipLocation, '.zip');
            $this->date = $this->getConfigDateFromFilename();
            $explodedTitle = explode('.', $this->title);
            $this->channel = $explodedTitle[1];
            $this->entityId = $explodedTitle[2];

            $this->tracks = array();
            $numFiles = $zip->numFiles;
            for($i = 0, $numFiles; $i < $numFiles; $i++) {
                $file = $zip->statIndex($i);
                if($this->checkTrackExclusions) {
                    // Do not attempt to read files named a UID that is excluded
                    if(in_array(substr($file['name'], 11, 38), $this->excludeTrackUids))
                        continue;
                }
                if(substr($file['name'], 0, 11) == 'MediaClips\\') {
                    $fp = $zip->getStream($zip->getNameIndex($i));
                    $xml = fread($fp, $file['size']);
                    // Replace ampersands with &amp;
                    $xml = preg_replace('/&(?![#]?[a-z0-9]+;)/i', "&amp;$1", $xml);
                    $track = new Track;
                    if($track->importDjv($xml) !== false) {
                        $this->tracks[] = $track;
                    } #else {
                    #echo '<strong style="color: firebrick;">WARNING, INVALID XML IN FILE: ' . $file['name'] . '!!!</strong><br />';
                    #}
                    flush();
                    fclose($fp);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function getConfigDateFromDirName($dirName = '') {
        $dirName = (empty($dirName)) ? $this->title : $dirName;

        if(!empty($dirName)) {
            $dateParts = explode('-', $dirName);
            if(!empty($dateParts)) {
                $date = $dateParts[0] . '-' . $dateParts[1] . '-' . $dateParts[2];
                $date .= ' ';
                $date .= substr($dateParts[3], 0, 2);
                $date .= ':';
                $date .= substr($dateParts[3], 2, 2);
                return $date;
            }
        }

        return false;
    }
	
    public function loadFromDirectory($location = '', $excludeTrackUids = null) {
        if(!empty($location)) $this->location = $location;
        if(!empty($excludeTrackUids)) $this->excludeTrackUids = $excludeTrackUids;

        if(!empty($this->excludeTrackUids)) {
            $this->checkTrackExclusions = true;
        }

		if(is_dir($this->location)) {
            $this->title = basename($this->location);
            $this->date = $this->getConfigDateFromDirName();

            $this->tracks = array();
			$mediaClipDir = $this->location . DS . 'MediaClips';
            
			if($files = scandir($mediaClipDir)) {
				if(!empty($files)) {
					foreach($files as $file) {
                        if($file == '.' || $file == '..')
                            continue;

						if($this->checkTrackExclusions) {
							if(in_array(substr($file, 11, 38), $this->excludeTrackUids))
								continue;
						}
							
						#$fp = $zip->getStream($zip->getNameIndex($i));
                        /*$fp = fopen($mediaClipDir . $file, 'r');
						$xml = fread($fp, filesize($file));*/
                        $xml = file_get_contents($mediaClipDir . DS . $file);
						// Replace ampersands with &amp;
						$xml = preg_replace('/&(?![#]?[a-z0-9]+;)/i', "&amp;$1", $xml);
						$track = new Track;
						if($track->importDjv($xml) !== false) {
							$this->tracks[] = $track;
						} #else {
						#echo '<strong style="color: firebrick;">WARNING, INVALID XML IN FILE: ' . $file['name'] . '!!!</strong><br />';
						#}
						#flush();
						/*fclose($fp);*/
                        unset($xml, $track, $file);
					}
				}
            }

            return true;
        } else {
            return false;
        }
    }

    public function loadProfilesFromZip($zipLocation = '') {
        if (!empty($zipLocation))
            $this->zipLocation = $zipLocation;

        $zip = new \ZipArchive();
        if($zip->open($this->zipLocation)) {
            $this->title = basename($this->zipLocation, '.zip');
            $this->date = $this->getConfigDateFromFilename();
            $explodedTitle = explode('.', $this->title);
            $this->channel = $explodedTitle[1];
            $this->entityId = $explodedTitle[2];

            $this->profiles = array();
            $numFiles = $zip->numFiles;
            for($i = 0, $numFiles; $i < $numFiles; $i++) {
                $file = $zip->statIndex($i);
                if(substr($file['name'], 0, 8) == 'Profiles') {
                    $fp = $zip->getStream($zip->getNameIndex($i));
                    $xml = stream_get_contents($fp); //$xml = fread($fp, $file['size']);
                    // Replace ampersands with &amp;
                    $xml = preg_replace('/&(?![#]?[a-z0-9]+;)/i', "&amp;$1", $xml);
                    $profile = new Track;

                    if($profile->importDjvProfiles($xml) !== false)
                        $this->profiles[] = $profile;

                    flush();
                    fclose($fp);
                }
            }
            return true;
        }
        else {
            return false;
        }
    }
}

class Entity extends Data {
    public $id = 0;
    public $configDates;
    public $baseDir = '';
    public $companyId;
    public $entityId;
    public $excludeTrackUids;

    public function __construct() {
        parent::__construct();

        $configuration = Configuration::singleton();
        $this->baseDir = $configuration->systemsDirectory;
    }

    protected function _exportArray() {
        $this->_data = array();

        if(!empty($this->id)) $this->_data['CompanyMusicEntity']['id'] = $this->id;
        if(!empty($this->companyId)) $this->_data['CompanyMusicEntity']['company_id'] = $this->companyId;
        if(!empty($this->entityId)) $this->_data['CompanyMusicEntity']['entity_id'] = $this->entityId;

        if(!empty($this->configDates)) {
            $i = 0;
            foreach($this->configDates as $configDate) {
                $this->_data['MusicEntityConfigDate'][$i] = $configDate->save();

                if(!empty($this->id)) $this->_data['MusicEntityConfigDate'][$i]['company_music_entity_id'] = $this->id;

                $i++;
            }
        }

        parent::_exportArray();
    }

    public function loadFromDirectory($entityId = 0, $headersOnly = true) {
        if(!empty($entityId)) $this->entityId = $entityId;

        $directory = $this->baseDir . $this->entityId . '/MUSIC/';

        if($files = scandir($directory)) {
            foreach($files as $file) {
                $isZip = (strpos($file, '.zip'));

                if($isZip) {
                    $configDate = new ConfigDate();
                    if(!$headersOnly)
                        $configDate->loadFromZip($directory . $file, $this->excludeTrackUids);
                    else
                        $configDate->title = basename($directory . $file, '.zip');
                    $this->configDates[] = $configDate;
                }
            }

            if(!empty($this->configDates)) {
                return true;
            }
        }

        return false;
    }

    public function loadFromProfilesDirectory($entityId = 0, $headersOnly = true) {
        if(!empty($entityId)) $this->entityId = $entityId;

        $directory = $this->baseDir . $this->entityId . '/MUSIC/';

        if($files = scandir($directory)) {
            $latestConfigDate = 0;
            foreach($files as $file) {
                $isZip = strpos($file, '.zip');
                if($isZip) {
                    if (strpos($file, '.zip.'))
                        $isZip = false;
                }
                if($isZip) {
                    $configDate = new ConfigDate();
                    if(!$headersOnly)
                        $configDate->loadProfilesFromZip($directory . $file);
                    else
                        $configDate->title = basename($directory . $file, '.zip');
                    $this->configDates[] = $configDate;
                    if (strtotime($latestConfigDate) < strtotime(($configDate->date)))
                        $latestConfigDate = $configDate->date;
                }
            }
            if(!empty($this->configDates)) {
                foreach ($this->configDates as $configDate) {
                    $configDate->isLatest = ($configDate->date == $latestConfigDate) ? true : false;
                }
                return true;
            }
        }
        return false;
    }

    public function getLatestConfigDateFileName($entityId = 0) {
        if(!empty($entityId)) $this->entityId = $entityId;

        $directory = $this->baseDir . $this->entityId . '/MUSIC/';

        if($files = scandir($directory)) {
            $latestConfigZipDate = '1979-01-01 00:00';
            $latestConfigZip = '';
            foreach($files as $file) {
                $isZip = strpos($file, '.zip');
                if($isZip) {
                    $configDate = new ConfigDate;
                    $date = $configDate->getConfigDateFromFilename($file);
                    if(strtotime($date) > strtotime($latestConfigZipDate)) {
                        $latestConfigZipDate = $date;
                        $latestConfigZip = $file;
                    }
                }
            }

            return $latestConfigZip;
        }

        return false;
    }

    public function getLatestConfigDate($entityId = 0) {
        if(!empty($entityId)) $this->entityId = $entityId;

        $directory = $this->baseDir . $this->entityId . '/MUSIC/';

        $latestConfigZip = $this->getLatestConfigDateFileName();

        if(!empty($latestConfigZip)) {
            $configDate = new ConfigDate();
            $configDate->loadFromZip($directory . $latestConfigZip, $this->excludeTrackUids);
            return $configDate;
        } else {
            return false;
        }
    }
}
?>