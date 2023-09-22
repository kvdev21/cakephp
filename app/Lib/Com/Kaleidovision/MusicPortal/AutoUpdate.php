<?php
/**
 * This file contains classes which allow the Music Portal
 * to interact with the Auto Update server
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.AutoUpdate
 */

namespace Com\Kaleidovision\MusicPortal;

// Load configuration
require_once('Configuration.php');

class AutoUpdate {
    public $entityId = 0;
    public $configTypes;
    public $date;
    public $error = '';
    
    public function publishFile($fileLocation, $serverLocation, $entityId = 0, $date = null) {
        $this->entityId = (!empty($entityId)) ? $entityId : $this->entityId;
        $this->date = (!empty($date)) ? $date : $this->date;
        
        $configuration = Configuration::singleton();
        $url = 'http://' . $configuration->teleHouseIPAddress . '/utils/fileUpload.php';
        
        $formVars = array();
        $formVars['userfile'] = "@$fileLocation";
        $formVars['EntityId'] = $entityId;
        $formVars['date'] = $this->date;
        $formVars['filename'] = $serverLocation;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formVars);
        $result = curl_exec($ch);
        
        if($result == 'OK') {
            return true;
        } else {
            $error = curl_error($ch);
            if(!empty($err)) {
                $this->error = 'CURL Error: ' . $error;
            } else {
                $this->error = 'Bad server response.';
            }
        }
        
        return false;
    }
    
    public function hawk($configTypes = null, $entityId = 0) {
        $this->entityId = (!empty($entityId)) ? $entityId : $this->entityId;
        $this->configTypes = (!empty($configTypes)) ? $configTypes : $this->configTypes;
        
        // Get the system name
        $configuration = Configuration::singleton();
        $url = 'http://' . $configuration->dashBoardIPAddress . '/dashboard_production/external/unit/ext.unitOnline.php?ENTITY_ID=' . $this->entityId;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        
        if(!$result) {
            $this->error = curl_error($ch);
            return false;
        }
        
        if(!$xml = new \SimpleXMLElement($result)) {
            $this->error = 'Error loading XML.';
            return false;
        }
        $systemName = $xml['unit-name'];
        
        // TODO: Remove hard coded system name
        #$systemName = 'K402530'; // AH desk karaoke test system
        #$systemName = 'K424540'; // Downstairs test system Bjorn
		#$systemName = 'K398540'; // Bjorn Desk
		
        // Parse config types
        if(!empty($this->configTypes)) {
            $types = '';
            foreach($this->configTypes as $configType) {
                $types .= "TYPES[]%3D{$configType}%26";
            }
        } else {
            $types = 'TYPES[]%3DKV-CORE%26TYPES[]%3DBSM%26TYPES[]%3DMUSIC%26TYPES[]%3DUPS%26'; // All
        }
        
        // Hawk it
        $url = 'http://' . $configuration->teleHouseIPAddress . "/kvAutoUpdate/ui/hawk_commands.php?dontShowOutput=1&inSERIALNOs[]={$systemName}&OPTION=1&PARAMS[clearOtherMatching]=YES&actionSTRING=MF_IMAGE_LEGACY&PARAMS[MANIFEST_FILENAME]=manifest_create.php%3F$types";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        
        if(substr_count($result, 'OK') > 0) {
            return true;
        } else {
            $error = curl_error($ch);
            if(!empty($err)) {
                $this->error = $error;
            } else {
                $this->error = 'Bad server response.';
            }
        }
    }
}
?>
