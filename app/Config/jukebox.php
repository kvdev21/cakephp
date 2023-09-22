<?php
App::import('KL4', 'AutoUpdateFolders');

Configure::load('kv_runtime');
$runtime = Configure::read('KVRuntime');

// TODO: Load this from KL4.Jukebox.xml
$config = array(
    'Jukebox' => array(
        // Static values
        'appVersion' => '1.3',
        'appInstallFile' => WWW_ROOT . 'files' . DS . 'install' . DS . 'installed.txt',
        'appSchemaFile' => APP . 'Config' . DS . 'Schema' . DS . 'jukebox.sql',
        'wampMysqlDir' => 'C:' . DS . 'wamp' . DS . 'bin' . DS . 'mysql',
        'clixBinary' => APP . 'Vendor' . DS . 'Kaleidovision' . DS . 'clixcl.exe',
        'configIndex' => '-15',
        'coreConfigDir' => '',
        'bsmConfigDir' => '',
        'upsConfigDir' => '',
        'musicConfigDir' => '',
        'musicConfigPath' => null,
        'localConfigPath' => null,
        // Dynamic (determined by system)
        'url' => 'http://' . $runtime['ip'] . '/Jukebox',
        'decryptedClipsDir' => WWW_ROOT . 'files' . DS . 'decrypted-clips',
        'waveformTmpDir' => WWW_ROOT . 'files' . DS . 'waveform-tmp',
        'waveformDir' => WWW_ROOT . 'img' . DS . 'waveforms',
        // Configurable in KL4.Jukebox.xml
        'app' => array(
            'filters' => array(
                'commercial dance' => array('imotion commercial dance'),
                'dance classics' => array('imotion dance classics'),
                'cool pop' => array('imotion cool pop'),
                'electro pop' => array('imotion electro pop'),
                'indie' => array('imotion indie'),
                'deep house' => array('imotion deep house'),
                'funky house' => array('imotion funky house'),
                'RnB' => array('imotion RnB'),
                'retro' => array('imotion retro'),
                'rock' => array('imotion rock')
            ),
            'clipsDir' => 'C:' . DS . 'clips',
            'holdingClipDir' => 'C:' . DS . 'clips',
            'holdingClipFilename' => '33740_logo.mpe',
        ),
        'services' => array(
            'silvaIp' => '127.0.0.1',
            'silvaPort' => '11500',
            'musicSchedulerIp' => '127.0.0.1',
            'musicSchedulerPort' => '3002',
            'musicSchedulerHttpPort' => '1000',
        )
    )
);

$auFolders = new \Com\Kaleidovision\KL4\AutoUpdateFolders();
$config['Jukebox']['configIndex'] = $auFolders->configIndex;

$config['Jukebox']['coreConfigDir'] = $auFolders->coreConfigDir;
$config['Jukebox']['bsmConfigDir'] = $auFolders->bsmConfigDir;
$config['Jukebox']['upsConfigDir'] = $auFolders->upsConfigDir;
$config['Jukebox']['musicConfigDir'] = $auFolders->musicConfigDir;

$config['Jukebox']['musicConfigPath'] = dirname(dirname(dirname($config['Jukebox']['coreConfigDir']))) . DS . 'music' . DS . 'Channel1' . DS . $config['Jukebox']['musicConfigDir'];

if(is_dir('D:\clips')) $config['Jukebox']['app']['clipsDir'] = 'D:\clips';

unset($auFolders);

$config['Jukebox']['appConfigFile'] = $config['Jukebox']['coreConfigDir'] . DS . 'KL4.Jukebox.xml';

// Load from config XML
if($data = file_get_contents($config['Jukebox']['appConfigFile'])) {
    $xml = new SimpleXMLElement($data);
    $config['Jukebox']['appConfigXml'] = $xml;

    if($cfg = $xml->{'app-config'}) {
        foreach($config['Jukebox']['app'] as $key => &$item) {
            if($key == 'filters') {
                try {
                    $filterCfg = $xml->{'app-config'}->{'filters'}->{'filter'};
                    if($filterCfg->count() > 0) {
                        $item = array();

                        foreach($filterCfg as $filter) {
                            $item[(string)$filter['name']] = array();

                            foreach($filter->{'tag'} as $tag) {
                                $item[(string)$filter['name']][] = (string) $tag;
                            }
                        }
                    }
                } catch(Exception $e) {
                    $this->log('Exception loading filters from XML: ' . $e->getMessage(), 'config');
                }
            } else {
                if(!empty($cfg->{MonkeySpaceMan::dashatize($key)}))
                    $item = (string) $cfg->{MonkeySpaceMan::dashatize($key)};
            }
        }
    }

    if($cfg = $xml->{'services-config'}) {
        foreach($config['Jukebox']['services'] as $key => &$item) {
            if(!empty($cfg->{MonkeySpaceMan::dashatize($key)}))
                $item = (string) $cfg->{MonkeySpaceMan::dashatize($key)};
        }
    }

    unset($xml, $cfg);
}

/*debug($config['Jukebox']['app']);
exit;*/