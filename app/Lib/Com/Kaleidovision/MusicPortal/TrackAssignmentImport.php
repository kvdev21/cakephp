<?php
/**
 * User: mhoffmann
 * Date: 07/08/12
 * Time: 09:47
 * This file contains classes which allow track assignments
 * to be imported
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.TrackAssignmentImport
 */

namespace Com\Kaleidovision\MusicPortal;

require_once('Data.php');

// Load configuration
require_once('Configuration.php');

/**
 * TrackAssignment class
 * @package TrackAssignment
 */
class TrackAssignment extends Data {
    public $file = '';
    public $trackName = '';
    public $trackArtist = '';
    public $trackAlbum = '';
    public $trackYear = 0;
    public $tagName = '';
    public $uid = '';

    public function importXML($xmlFile = null) {
        if (!file_exists($xmlFile))
            return false;

        if ($fp=fopen($xmlFile, "r")) {
            $xml = stream_get_contents($fp);
            $xml = preg_replace('/&(?![#]?[a-z0-9]+;)/i', "&amp;$1", $xml);
            echo "<br/>Loading XML";
            if (!$this->_xml->loadXML($xml)) {
                echo "...failed";
                return false;
            }
            else {
                echo "...success<br/>";
                if ($nodes = $this->_xml->getElementsByTagName('dict')->item(1)) {
                    echo "<br/>DICT NODE";
                    $childNodes = $nodes->getElementsByTagName('dict');
                    echo "<br/>CHILDREN: " . $childNodes->length;
                    foreach ($childNodes as $node) {
                        echo "<br/>CAT: " . $node->nodeName;
/*
                        if ($node->hasChildNodes) {
                            echo "<br/>HAS Children";
                        }
                        else
                            echo "<br/>NO Children";
  */
                    }
                }
            }

        }
        else
            echo "failed";
        //$this->loadXmlFile($xmlFile);
        //echo "<br/>trackassignmentimport: Loaded file<br/>";
        //$this->file = $fileName;
        //echo $this->_xml;
/*
        if ($nodes = $this->_xml->getElementsByTagName('MEDIA-CLIP')) {
            if ($attributes = $nodes->item(0)->attributes) {
                if ($node = $attributes->getNamedItem('UID')) {
                    $this->uid = $node->nodeValue;
                }
            }

            if ($playNodes = $nodes->item(0)->getElementsByTagName('PLAY')) {
                if ($playNodes->length==0)
                    return false;

                //echo "<br/>profile: " . $this->uid;
                for ($playNodesIdx=0; $playNodesIdx<$playNodes->length; $playNodesIdx++) {
                    if ($playClipNodes = $playNodes->item($playNodesIdx)->getElementsByTagName('PLAY-MEDIA-CLIP')) {
                        if ($attributes = $playClipNodes->item(0)->attributes)
                            $this->uids[] = $attributes->getNamedItem('UID')->nodeValue;
                    }
                }
            }

            if ($infoNodes = $nodes->item(0)->getElementsByTagName('INFO')) {
                if ($attributes = $infoNodes->item(0)->attributes) {
                    // profile name
                    if ($node = $attributes->getNamedItem('NAME')) {
                        $this->trackName = urldecode($node->nodeValue);
                    }
                    // start date
                    if ($node = $attributes->getNamedItem('INDATE'))
                        $this->startDate = $this->_formatDateString($node->nodeValue);
                    //end date
                    if ($node = $attributes->getNamedItem('OUTDATE'))
                        $this->endDate = $this->_formatDateString($node->nodeValue);
                    // start time
                    if ($node = $attributes->getNamedItem('STARTTIME'))
                        $this->startTime = $this->_formatTimeString($node->nodeValue);
                    // start time camelcase
                    if ($node = $attributes->getNamedItem('StartTime'))
                        $this->startTime = $this->_formatTimeString($node->nodeValue);
                    // end time
                    if ($node = $attributes->getNamedItem('FINISHTIME'))
                        $this->finishTime = $this->_formatTimeString($node->nodeValue);
                    // end time camelcase
                    if ($node = $attributes->getNamedItem('FinishTime'))
                        $this->finishTime = $this->_formatTimeString($node->nodeValue);
                    // day of the week
                    if ($node = $attributes->getNamedItem('DAYOFWEEK'))
                        $this->dow = $node->nodeValue;
                    // day of the week camelcase
                    if ($node = $attributes->getNamedItem('DayOfWeek'))
                        $this->dow = $node->nodeValue;
                }
            }
            return true;
        }
*/
    }
}
