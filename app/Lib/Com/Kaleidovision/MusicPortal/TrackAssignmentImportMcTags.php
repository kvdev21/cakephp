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
    public $trackAssignments;

    public function importXML($xmlFile = null) {
        if (!file_exists($xmlFile))
            return false;

        if ($fp=fopen($xmlFile, "r")) {
            $xml = stream_get_contents($fp);
            $xml = preg_replace('/&(?![#]?[a-z0-9]+;)/i', "&amp;$1", $xml);
            if (!$this->_xml->loadXML($xml)) {
                return false;
            }
            else {
                $i=0;
                $this->trackAssignments = array();
                if ($nodes = $this->_xml->getElementsByTagName('dict')->item(1)) {
                    $dictNodes = $nodes->getElementsByTagName('dict');
                    foreach ($dictNodes as $dictnode) {
                        $childNodes = $dictnode->getElementsByTagName('*');
                        foreach ($childNodes as $childNode) {
                            // check if the node is a key
                            if ($childNode->nodeName=='key') {
                                if ($childNode->nodeValue=='Name') {
                                    // track name
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['name'] = $node->nodeValue;
                                }
                                else if ($childNode->nodeValue=='Artist') {
                                    // artist
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['artist'] = $node->nodeValue;
                                }
                                else if ($childNode->nodeValue=='Album') {
                                    // Album
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['album'] = $node->nodeValue;
                                }
                                else if ($childNode->nodeValue=='Year') {
                                    // year
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['year'] = $node->nodeValue;
                                }
                                else if ($childNode->nodeValue=='Genre') {
                                    // tagpot name
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['tagpot'] = $node->nodeValue;
                                }
                                else if ($childNode->nodeValue=='Grouping') {
                                    // Vocal/Instrumental
                                    $node = $childNode->nextSibling;
                                    $this->trackAssignments[$i]['vocalinstrumental'] = $node->nodeValue;
                                }
                            }
                        }
                        $i++;
                    }
                }
            }
        }
    }
}
?>