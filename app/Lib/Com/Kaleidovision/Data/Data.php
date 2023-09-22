<?php
/**
 * @package Com.Kaleidovision.KL4
 * @subpackage Com.Kaleidovision.KL4.Clip
 */

namespace Com\Kaleidovision\Data;

abstract class Data {
    public $id = 0;
    public $errors; // For storing error messages

    protected $_xml;
    protected $_xpath;

    protected $_data;

    public function __construct() {
        $this->_xml = new \DOMDocument('1.0');

        $this->errors = array();
    }

    /**
     * Builds the data object properties based on the _xml DOMDocument
     */
    protected function _importDom() {
    }

    /**
     * Creates the _xml DOMDocument based on the data object properties
     */
    protected function _exportDom() {
    }

    /**
     * Builds the data object properties based on the _data array
     */
    protected function _importArray() {
    }

    /**
     * Creates the _data array based on the data object properties
     */
    protected function _exportArray() {
    }

    /**
     * Opens an XML file and updates the object based on its content
     * @param string $fileName File name
     */
    public function loadXmlFile($fileName) {
        if(file_exists($fileName)) {
            $this->_xml->load($fileName);
            return $this->loadXml($this->_xml->saveXml());
        }
    }

    /**
     * Loads an object from an XML string
     * @param string $xml XML string
     */
    public function loadXml($xml = null) {
        if(!empty($xml)) {
            $this->_xml->loadXML($xml);
            return $this->_importDom();
        }
    }

    /**
     * Saves the object as an XML file if fileName is specified
     * otherwise returns the XML as a string
     * @param mixed $fileName Name of file to save to
     * @return mixed XML string or status of save to file
     */
    public function saveXml($fileName = null) {
        $this->_exportDom();

        $this->fileName = $fileName;

        $this->_xml->formatOutput = true;

        if(empty($this->fileName)) {
            return $this->_xml->saveXML();
        } else {
            return $this->_xml->save($this->fileName);
        }
    }

    /**
     * Same as loadXml but loads from a DOMDocument object
     * @param DOMDocument XML object
     */
    public function loadDom(\DOMNode $domNode) {
        if(is_a($domNode, '\DOMDocument')) {
            $this->_xml = $domNode;
        } elseif(is_a($domNode, '\DOMNode')) {
            $this->_xml->appendChild($this->_xml->importNode($domNode, true));
        }

        $this->_importDom();
    }

    /**
     * Same as saveXml but returns a DOMDocument object
     * @return DOMDocument XML object
     */
    public function saveDom() {
        $this->_exportDom();

        return $this->_xml;
    }

    /**
     * Loads the object based on it's CakePHP data array representation
     * @param array $data CakePHP data array
     */
    public function load(array $data) {
        $this->_data = $data;

        $this->_importArray();
    }

    /**
     * Saves the object data using the supplied CakePHP AppModel
     * otherwise return the object as a CakePHP data array
     * @return array CakePHP data array
     */
    public function save(\AppModel &$model = null) {
        $this->_exportArray();

        if(is_a($model, 'AppModel')) {
            $model->data = $this->_data;
            return $model->saveAssociated(null, array('deep' => true));
        } else {
            if(is_array($this->_data)) {
                return $this->_data;
            }
        }
    }
}
