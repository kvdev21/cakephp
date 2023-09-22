<?php
/**
 * This file contains classes which allow Mediaclip
 * data to be stored and manipulated
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.Mediaclip
 */

namespace Com\Kaleidovision\MusicPortal;

require_once('Data.php');
require_once('AutoUpdate.php');

// Load configuration
require_once('Configuration.php');

/**
 * Mediaclip class
 * @package Mediaclip
 */
class Mediaclip extends Data {
    public $uid = '';
    public $cliptype = '';
    public $sourcefilename = '';
    public $sourcefiletime = null;
    public $sourcefilesize = null;
    public $artist = '';
    public $trackname = '';
    public $album = '';
    public $bpm = null;
    public $genre1 = null;
    public $genre2 = null;
    public $genre3 = null;
    public $year = null;
    public $copyrightholder = '';
    public $source = '';
    public $isrc = '';
    public $duration = null;
    public $createdby = null;
    public $createdate = null;
    public $lastchangedby = null;
    public $lastchangedate = null;
    public $deletedby = null;
    public $deletedate = null;
    public $vocalinstrumental = null;

    protected function _importDom() {
        parent::_importDom();
/*
        if($nodes = $this->_xml->getElementsByTagName('song')) {
            if($attributes = $nodes->item(0)->attributes) {
                if($node = $attributes->getNamedItem('titl'))
                    $this->trackName = $node->nodeValue;
                if($node = $attributes->getNamedItem('bpmt'))
                    $this->bpm = $node->nodeValue;
                if($node = $attributes->getNamedItem('year'))
                    $this->year = $node->nodeValue;
                if($node = $attributes->getNamedItem('time'))
                    $this->length = date('H:i:s', strtotime($node->nodeValue));
                if($node = $attributes->getNamedItem('fnam'))
                    $this->file = $node->nodeValue;
                if($node = $attributes->getNamedItem('arti'))
                    $this->trackArtist = $node->nodeValue;
            }
        }
*/
    }
    
    protected function _exportDom() {
/*
      $this->_xml = new \DOMDocument('1.0');
        $mediaclip = $this->_xml->appendChild($this->_xml->createElement('song'));
        
        $mediaclip->appendChild($this->_xml->createAttribute('titl'))->appendChild($this->_xml->createTextNode($this->trackName));
        $mediaclip->appendChild($this->_xml->createAttribute('bpmt'))->appendChild($this->_xml->createTextNode($this->bpm));
        $mediaclip->appendChild($this->_xml->createAttribute('year'))->appendChild($this->_xml->createTextNode($this->year));
        $mediaclip->appendChild($this->_xml->createAttribute('time'))->appendChild($this->_xml->createTextNode($this->length));
        $mediaclip->appendChild($this->_xml->createAttribute('fnam'))->appendChild($this->_xml->createTextNode($this->file));
        $mediaclip->appendChild($this->_xml->createAttribute('arti'))->appendChild($this->_xml->createTextNode($this->trackArtist));
        
        parent::_exportDom();
*/
    }
    
    protected function _importArray() {
        parent::_importArray();
/*
        // Fill mediaclip details
        $this->id = (!empty($this->_data['Mediaclip']['Mediaclip']['id'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['id'] : 0;
        $this->trackName = (!empty($this->_data['Mediaclip']['Mediaclip']['track_name'])) ? (string) $this->_data['Mediaclip']['Mediaclip']['track_name'] : '';
        $this->bpm = (!empty($this->_data['Mediaclip']['Mediaclip']['bpm'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['bpm'] : 0;
        $this->year = (!empty($this->_data['Mediaclip']['Mediaclip']['year'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['year'] : 0;
        $this->high = (!empty($this->_data['Mediaclip']['Mediaclip']['high'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['high'] : 0;
        $this->length = (!empty($this->_data['Mediaclip']['Mediaclip']['length'])) ? (string) $this->_data['Mediaclip']['Mediaclip']['length'] : '';
        $this->file = (!empty($this->_data['Mediaclip']['Mediaclip']['file'])) ? (string) $this->_data['Mediaclip']['Mediaclip']['file'] : '';
        $this->rating = (!empty($this->_data['Mediaclip']['Mediaclip']['rating'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['rating'] : 0;
        $this->reviewCount = (!empty($this->_data['Mediaclip']['Mediaclip']['review_count'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['review_count'] : 0;
        
        $this->uid = (!empty($this->_data['Mediaclip']['Mediaclip']['uid'])) ? (int) $this->_data['Mediaclip']['Mediaclip']['uid'] : '';
        $this->trackArtist = (!empty($this->_data['Mediaclip']['Mediaclip']['track_artist'])) ? (string) $this->_data['Mediaclip']['Mediaclip']['track_artist'] : '';
*/
    }

    public function asCakeArray() {
      $this->_exportArray();
      return $this->_data;
    }
    
    protected function _exportArray() {
        $this->_data = array(
            'Clip' => array(
                'uid' => $this->uid,
                'cliptype' => $this->cliptype,
                'sourcefilename' => $this->sourcefilename,
                'sourcefiletime' => $this->sourcefiletime,
                'sourcefilesize' => $this->sourcefilesize,
                'artist' => $this->artist,
                'trackname' => $this->trackname,
                'album' => $this->album,
                'bpm' => $this->bpm,
                'genre1' => $this->genre1,
                'genre2' => $this->genre2,
                'genre3' => $this->genre3,
                'yearoffirstpublication' => $this->year,
                'copyrightholder' => $this->copyrightholder,
                'source' => $this->source,
                'isrc' => $this->isrc,
                'duration' => $this->duration,
                'createdby' => $this->createdby,
                'createdate' => $this->createdate,
                'lastchangedby' => $this->lastchangedby,
                'lastchangedate' => $this->lastchangedate,
                'deletedby' => $this->deletedby,
                'deletedate' => $this->deletedate,
                'vocalinstrumental' => $this->vocalinstrumental
            )
        );
        
        if(!empty($this->id)) $this->_data['Clip']['id'] = $this->id;

        if ($this->_data['Clip']['sourcefiletime'] == 0) {
          $this->_data['Clip']['sourcefiletime'] = null;
        }
        
        parent::_exportArray();
    }

    public function loadDJVFile($fileName) {
        if(file_exists($fileName)) {
            $this->_xml->load($fileName);
            $this->importDjv($this->_xml->saveXml());
        }
    }
    
    public function importDjv($xml = null) {
        if(empty($xml)) return false;
        if(!$this->_xml->loadXML($xml)) return false;
        
        if($nodes = $this->_xml->getElementsByTagName('MEDIA-CLIP')) {
          if($attributes = $nodes->item(0)->attributes) {
            if($node = $attributes->getNamedItem('UID'))
              $this->uid = $node->nodeValue;
            if($node = $attributes->getNamedItem('CLIPTYPE'))
              $this->cliptype = $node->nodeValue;
          }
            
          $playNodes = $nodes->item(0)->getElementsByTagName('PLAY');
          if ($playNodes->length > 0) {
            $playClipNodes = $playNodes->item(0)->getElementsByTagName('PLAY-CLIP');
              if ($playClipNodes->length > 0) {
                $attributes = $playClipNodes->item(0)->attributes;
                if ($attributes->length > 0) {
                  $this->sourcefilename = html_entity_decode(urldecode($attributes->getNamedItem('FILE-NAME')->nodeValue));
                  $this->sourcefilesize = (int) $attributes->getNamedItem('FILE-SIZE')->nodeValue;
                  $this->sourcefiletime = (int) $attributes->getNamedItem('FILE-DATE')->nodeValue;
                }
                if(($startNode = $attributes->getNamedItem('START'))
                  && ($finishNode = $attributes->getNamedItem('FINISH'))) {
                  $milliseconds = $finishNode->nodeValue - $startNode->nodeValue;

                  $hours = floor($milliseconds / (1000*60*60));
                  $minutes = floor(($milliseconds % (1000*60*60)) / (1000*60));
                  $seconds = floor((($milliseconds % (1000*60*60)) % (1000*60)) / 1000);

                  //$this->duration = str_pad($hours, 2, 0, STR_PAD_LEFT) . ':' . str_pad($minutes, 2, 0, STR_PAD_LEFT) . ':' . str_pad($seconds, 2, 0, STR_PAD_LEFT);
                  $this->duration = $milliseconds;
                }
              }
          }
            
          if($infoNodes = $nodes->item(0)->getElementsByTagName('INFO')) {
            if($attributes = $infoNodes->item(0)->attributes) {
              if($node = $attributes->getNamedItem('ARTIST'))
                $this->artist = urldecode($node->nodeValue);
              if($node = $attributes->getNamedItem('NAME'))
                $this->trackname = urldecode($node->nodeValue);
              if($node = $attributes->getNamedItem('ALBUM'))
                $this->album = urldecode($node->nodeValue);
              if($node = $attributes->getNamedItem('BPM'))
                $this->bpm = $node->nodeValue;
              if($node = $attributes->getNamedItem('GENRE1'))
                $this->genre1 = $node->nodeValue;
              if($node = $attributes->getNamedItem('GENRE2'))
                $this->genre2 = $node->nodeValue;
              if($node = $attributes->getNamedItem('GENRE3'))
                $this->genre3 = $node->nodeValue;
              if($node = $attributes->getNamedItem('YEAR'))
                $this->year = $node->nodeValue;
              if($node = $attributes->getNamedItem('COPYRIGHTHOLDER'))
                $this->copyrightholder = $node->nodeValue;
              if($node = $attributes->getNamedItem('SOURCE'))
                $this->source = $node->nodeValue;
                if($node = $attributes->getNamedItem('ISRC'))
                    $this->isrc = $node->nodeValue;
                if($node = $attributes->getNamedItem('VOCALINSTRUMENTAL'))
                    $this->vocalinstrumental = $node->nodeValue;
            }
          }
        }
    }
}

?>