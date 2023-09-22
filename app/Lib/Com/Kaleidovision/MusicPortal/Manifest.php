<?php
/**
 * This file contains classes which allow manifest
 * data to be stored and manipulated
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.Manifest
 */
 
namespace Com\Kaleidovision\MusicPortal;

require_once('Data.php');

class Manifest extends Data {
    
	public $manifestFile;
	public $playlists;

	function __construct(){
		parent::__construct();
		$configuration = Configuration::singleton();
		$this->manifestFile = $configuration->playlistManifestFile;
		if (!file_exists($this->manifestFile)) {
			$this->saveXml($this->manifestFile);
		}	
	}
	
    protected function _importDom() {
        parent::_importDom();

		if ($nodes = $this->_xml->getElementsByTagName('playlists')) {
			if ($playlists = $nodes->item(0)->getElementsByTagName('playlist')) {
				$this->playlists = array();
				foreach ($playlists as $playlist) {
					$ManifestItem = new ManifestItem();
					$ManifestItem->loadDom($playlist);
					$this->playlists[] = $ManifestItem;
				}
			}
		}

        return true;
    }

    protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');
		
		$playlists = $this->_xml->appendChild($this->_xml->createElement('playlists'));
		
        if (!empty($this->playlists) && is_array($this->playlists)) {
            foreach ($this->playlists as $playlist) {
                $playlists->appendChild($this->_xml->importNode($playlist->saveDom()->firstChild, true));
            }
        }
		
        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

    }

    protected function _exportArray() {

        parent::_exportArray();
    }
	
	function getManifestById($mpid){
		foreach($this->playlists as $playlist){
			if ($playlist->mp_id == $mpid){
				return $playlist;
			}
		}
		return false;
		/*
		$this->_xpath = new \DOMXPath($this->_xml);
		$nodes = $this->_xpath->query("//playlists/playlist[@mp-id=$mpid]");
		
		if ($nodes->length > 0){
			return $nodes->item(0);
		} else {
			return false;
		}
		*/
	}
	
	function getManifestByFilename($filename){
		$filename = str_replace('/','\\',$filename);
		foreach($this->playlists as $playlist){
			if ($playlist->file_name == $filename){
				return $playlist;
			}
		}
		return false;
	}	
	 
	function addManifestItem(ManifestItem $manifestItem){
		$this->playlists[] = $manifestItem;
	}
}

class ManifestItem extends Data{
	public $mp_id = 0;
    public $user_id = 0;
    public $file_name = ''; 
	
	protected function _importDom(){
		parent::_importDom();
		if ($nodes = $this->_xml->getElementsByTagName('playlist')) {
			if ($attributes = $nodes->item(0)->attributes) {
				if ($node = $attributes->getNamedItem('file-name'))
					$this->file_name = $node->nodeValue;
				if ($node = $attributes->getNamedItem('mp-id'))
					$this->mp_id = $node->nodeValue;
				if ($node = $attributes->getNamedItem('user-id'))
					$this->user_id = $node->nodeValue;
			}
		}
	}
	
	protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');
        $track = $this->_xml->appendChild($this->_xml->createElement('playlist'));

        $track->appendChild($this->_xml->createAttribute('file-name'))->appendChild($this->_xml->createTextNode(str_replace('/','\\',$this->file_name)));
        $track->appendChild($this->_xml->createAttribute('mp-id'))->appendChild($this->_xml->createTextNode($this->mp_id));
        $track->appendChild($this->_xml->createAttribute('user-id'))->appendChild($this->_xml->createTextNode($this->user_id));
     
        parent::_exportDom();
    }
}
?>