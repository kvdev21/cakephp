<?php
/**
 * This file contains classes which allow
 * smart filters to be stored and manipulated
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.SmartfilterObject
 */

namespace Com\Kaleidovision\MusicPortal;

require_once('SmartfilterBaseObjects.php');

class SmartFilterItems extends SmartFilterBase {
    public $condition;
    public $level;
    public $smartfilteritems;
    private $arrOperator = array(
        'and' => 'all',
        'or' => 'any'
    );
    private $arrInputValues = array(
        'TrackArtist.name' => array('value' => 'Artist', 'type' => 'string'),
        'Track.track_name' => array('value' => 'Track Name', 'type' => 'string'),
        'Track.bpm' => array('value' => 'BPM', 'type' => 'integer'),
        'Track.length' => array('value' => 'Length', 'type' => 'integer'),
        'Track.rating' => array('value' => 'Rating', 'type' => 'integer'),
        'Track.year' => array('value' => 'Year', 'type' => 'integer'),
        'Genre1.title' => array('value' => 'Genre 1', 'type' => 'string'),
        'Genre2.title' => array('value' => 'Genre 2', 'type' => 'string'),
        'Genre3.title' => array('value' => 'Genre 3', 'type' => 'string'),
        'AddedOnConfigDate.date' => array('value' => 'Added On', 'type' => 'date'),
        'Track.track_review_count' => array('value' => 'Number of Reviews', 'type' => 'integer')
    );
    private $arrInputConditions = array(
        'integer' => array(
            'equal' => 'is',
            'notequal' => 'is not',
            'lessthan' => 'is less than',
            'greaterthan' => 'is greater than'
        ),
        'string' => array(
            'is' => 'is',
            'isnot' => 'is not',
            'contains' => 'contains',
            'startswith' => 'starts with',
            'endsin' => 'ends in'
        ),
        'date' => array(
            'equal' => 'on',
            'notequal' => 'not on',
            'lessthan' => 'before',
            'greaterthan' => 'after',
            'onorlessthan' => 'on or before',
            'onorgreaterthan' => 'on or after'
        )
    );

    /* CREATE THE XML FROM THE HTML */

    /* return the xml from the html*/
    public function html2xml($html) {
        $xmlString = '';
        $this->_processHtml2Xml($html, '', $xmlString, 0);
        return $xmlString;
    }

    /* CREATE THE HTML FROM THE XML */

    /* return the html from the xml */
    public function xml2html($xml) {
        $htmlString = '';
        if($this->_processXml2Html($xml, '', $htmlString, 0, 0) !== false) {
            return $htmlString;
        } else {
            return false;
        }
    }

    /* return the xml as html */
    protected function _processXml2Html($xml, $condition = '', &$htmlString, $level, $nameIdx) {
        $oXml = new \SimpleXMLElement($xml);
        $firstpass = true;
        $idx = 0;
        if($oXml->count() > 0) {
            foreach ($oXml as $node) {
                if ($node->getName() == 'items') {
                    $currentCondition = $node['type'];
                    $htmlString .= '<div class="sfItemsGroup"><div class="sfItemsAndOr">' . $this->_getOperatorHtml((string)$currentCondition, $level, $nameIdx) . '</div><div class="sfItemsLines"><div class="sfIndent"></div><div class="sfItems">';
                    $nameIdx = $this->_processXml2Html($node->asXML(), $currentCondition, $htmlString, $level + 1, $nameIdx + 1);
                    $htmlString .= '</div></div></div>';

                } else {
                    $s = '<div class="sfItem" level="' . $level . '">' . $this->_getFieldInputHtml((string)$node['field'], $level, $nameIdx) . $this->_getConditionInputHtml((string)$node['field'], (string)$node['condition'], $level, $nameIdx) . $this->_getValueInputHtml((string)$node['value'], $level, $nameIdx) . '<div class="sfButtons">' . $this->_getSFButtons($level, $nameIdx) . '</div></div>';

                    $htmlString .= $s;
                    $firstpass = false;
                    $nameIdx++;
                }
                $idx++;
            }
        } else {
            return false;
        }
        return $nameIdx;
    }

    /* return the AND/OR dropdown as html */
    protected function _getOperatorHtml($operatorItem, $level, $id) {
        $s = '<select class="sfSelectOperator" name="selectOperator_' . $level . '_' . $id . '">';
        //$s = '<select class="sfSelectOperator" name="selectOperator_' . $level . '_[]">';
        foreach ($this->arrOperator as $key => $value) {
            $selected = '';
            if ($this->_stringsMatch($key, $operatorItem)) {
                $selected = 'selected="selected"';
            }
            $s .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        return $s . '</select>';
    }

    /* return the smart filter buttons */
    protected function _getSFButtons($level, $id, $minusStatus = 1, $plusStatus = 1, $expandStatus = 1) {
        $disableStatus = 'disabled="disabled"';
        $result = '<button type="button" title="Delete Entry" class="minus titletip" ' . ($minusStatus == 0 ? $disableStatus : '');
        $result .= '>-</button><button type="button" title="Add Entry" class="plus titletip" ' . ($plusStatus == 0 ? $disableStatus : '');
        $result .= '>+</button><button type="button" title="Add Sub Entry" class="expand titletip" ' . ($expandStatus == 0 ? $disableStatus
                : '') . '>...</button>';
        return $result;
    }

    /* return the field dropdown as html */
    protected function _getFieldInputHtml($selectedItem, $level, $id) {
        $s = '<select class="sfSelectField" name="selectField_' . $level . '_' . $id . '" >';
        //$s = '<select class="sfSelectField" name="selectField_' . $level . '_[]" >';
        foreach ($this->arrInputValues as $key => $value) {
            $selected = '';
            if ($this->_stringsMatch($key, $selectedItem))
                $selected = 'selected="selected"';
            $s .= '<option value="' . $key . '" ' . $selected . ' kvtype="' . $value['type'] . '">' . $value['value'] . '</option>';
        }
        return $s . '</select>';
    }

    /* return the condition dropdown as html */
    protected function _getConditionInputHtml($fieldItem, $selectedItem, $level, $id) {
        $s = '<select class="sfSelectCondition" name="selectCondition_' . $level . '_' . $id . '" >';
        //$s = '<select class="sfSelectCondition" name="selectCondition_' . $level . '_[]" >';
        $arrConditions = $this->arrInputConditions[$this->arrInputValues[$fieldItem]['type']];
        foreach ($arrConditions as $key => $value) {
            $selected = '';
            if ($this->_stringsMatch($key, $selectedItem))
                $selected = 'selected="selected"';
            $s .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        return $s . '</select>';
    }

    /* return the input value as html */
    protected function _getValueInputHtml($value, $level, $id) {
        return '<input type="text" name="inputValue_' . $level . '_' . $id . '" value="' . $value . '"/>';
        //return '<input type="text" name="inputValue_' . $level . '_[]" value="' . $value . '"/>';
    }


    /* CREATE AN SQL STATEMENT FROM THE XML */

    /* return the sql from the xml */
    public function xml2sql($xml) {
        $sqlString = '';
        $this->_processXml2Sql($xml, '', $sqlString);
        return $sqlString;
    }

    /* recursive function which traverses the xml nodes to create a sql statement */
    protected function _processXml2Sql($xml, $condition = '', &$sqlString) {
        $oXml = new \SimpleXMLElement($xml);
        $firstpass = true;
        foreach ($oXml as $node) {
            if ($node->getName() == 'items') {
                $currentCondition = ' ' . $node['type'] . ' ';
                if ($condition > '') {
                    $sqlString .= $condition;
                }
                $sqlString .= "(";
                $this->_processXml2Sql($node->asXML(), $currentCondition, $sqlString);
                $sqlString .= ")";
            } else {
                if (!$firstpass)
                    $sqlString .= $condition;

                $type = $this->arrInputValues[(string)$node['field']]['type'];

                $value = ($type == 'date') ? date('Y-m-d H:i:s', strtotime($node['value'])) : $node['value'];
                $isString = ($type == 'date' || $type == 'string');

                switch ($node['condition']) {
                    case 'greaterthan':
                        $itemCondition = '>';
                        break;
                    case 'lessthan':
                        $itemCondition = '<';
                        break;
                    case 'equal':
                        $itemCondition = '=';
                        break;
                    case 'notequal':
                        $itemCondition = '<>';
                        break;
                    case 'equalorgreaterthan':
                        $itemCondition = '>=';
                        break;
                    case 'equalorlessthan':
                        $itemCondition = '<=';
                        break;
                    case 'is':
                        $itemCondition = '=';
                        break;
                    case 'isnot':
                        $itemCondition = '<>';
                        break;
                    case 'contains':
                        $itemCondition = ' LIKE ';
                        $itemValue = "'%" . $value . "%'";
                        break;
                    case 'startswith':
                        $itemCondition = ' LIKE ';
                        $itemValue = "'" . $value . "%'";
                        break;
                    case 'endsin':
                        $itemCondition = ' LIKE ';
                        $itemValue = "'%" . $value . "'";
                        break;
                }

                if(empty($itemValue)) $itemValue = ($isString) ? "'$value'" : $value;

                $s = '(' . $node['field'] . $itemCondition . $itemValue . ')';
                $sqlString .= $s;
                $firstpass = false;
            }
        }
    }

    /* return the xml as html */
    protected function _processNewSmartFilter($level, $idx) {
        $divLevel = '<div class="sfLevel">';
        $divIndent = '<div class="sfNoIndent"></div>';
        $divButtons = '<div class="sfButtons">';
        $htmlString .= '<div class="sfLineGroup" sfLevel="' . $level . '" id="rule' . $idx . '">' . $divIndent . $divLevel . $this->_getOperatorHtml("AND", $level, $idx) . $divButtons . '</div></div></div>';
        $htmlString .= $divIndent . $divLevel;
        $divIndent = '<div class="sfIndent"></div>';
        $level++;
        $idx++;
        $htmlString .= '<div class="sfLine" sfLevel="' . $level . '" id="rule' . $idx . '">' . $divIndent . $divLevel . $this->_getFieldInputHtml("TrackArtist.name", $level, $idx) . ' ' . $this->_getConditionInputHtml("TrackArtist.name", "is", $level, $idx) . ' ' . $this->_getValueInputHtml("", $level, $idx) . $divButtons . $this->_getSFButtons($level, $idx, 0, 1, 1) . '</div></div></div>';
        $htmlString .= '</div>';
        return $htmlString;
    }

    /* return the default html for a smart filter */
    public function newSmartFilter() {
        $xmlString = '';
        return $this->_processNewSmartFilter(0, 0);
    }

}


?>