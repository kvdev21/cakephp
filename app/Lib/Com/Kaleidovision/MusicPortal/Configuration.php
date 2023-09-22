<?php
/**
 * This file contains the configuration class
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.Configuration
 */

namespace Com\Kaleidovision\MusicPortal;

class Configuration {
	public static $instance;

    // Used by Music Portal Server ONLY
    public $clipsDirectory = '/var/www/htdocs/kvAutoUpdate/publish/clips';
    public $systemsDirectory = '/var/www/htdocs/kvAutoUpdate/publish/configzips/';
    //public $clipsDirectory = '/home/htdocs/kvAutoUpdate/publish/clips';
    //public $systemsDirectory = '/home/htdocs/kvAutoUpdate/publish/configzips/';
    public $tempDirectory = '/tmp/mp/';
    public $teleHouseIPAddress = '192.168.10.39';
    public $dashBoardIPAddress = '192.168.0.97';
	
	// Used by Music Portal Client ONLY
    public $liveMusicPortalIPAddress = 'mp.kaleidovision.co.uk:8053';
    public $silvaPlaylistsDirectory = 'C:/Kaleidovision/local/Kl4UserProfiles/Playlists/';
    public $playlistManifestFile = 'C:/Kaleidovision/local/music_portal_manifest.xml';
    
	public $mpUserId;
	public $mpPassword;
	    
    private function __construct() {
        if(!empty($GLOBALS['VenueData']['Config']['kv']['LatestDate'])) {
            $coresFile = $GLOBALS['VenueData']['Config']['kv']['LatestDate'] . '\KL4.MusicPortal.xml';
            $coresDocument = new \DOMDocument();
            if($coresDocument->load($coresFile)) {
                $xpath = new \DOMXPath($coresDocument);
                $mpSettingsResult = $xpath->query('//Settings');
                if($mpSettingsResult->length > 0) {
                    $settings = $mpSettingsResult->item(0);

                    if($value = $settings->getAttribute('MPUSERID'))
                        $this->mpUserId = $value;
                    if($value = $settings->getAttribute('MPPASSWORD'))
                        $this->mpPassword = $value;
                }
            }
        }
		
        switch($_SERVER['SERVER_ADDR']) {
            case '127.0.0.1':
			case '192.168.0.124':
            case '192.168.0.159':
			case '192.168.0.163':
                $this->clipsDirectory = 'W:/clips';
                $this->systemsDirectory = 'U:/Publish/Systems/';
                $this->tempDirectory = 'C:/TEMP/MP/';
                break;
            case '192.168.0.51':
                $this->clipsDirectory = '/mnt/kvsan4';
                $this->systemsDirectory = '/mnt/kvsan4/vd005-programs/programs/Publish/Systems/';
                break;
        }

        if(!is_dir($this->tempDirectory))
            mkdir($this->tempDirectory, 0777, true);
    }
    
    public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    public function __clone(){
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
}
?>