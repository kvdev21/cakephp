<?php
App::uses('HttpSocket', 'Network/Http');

class SilvaComponent extends Component {
    private $__appConfig;
    private $__httpSocket;

    public function initialize(Controller $controller) {
        Configure::load('jukebox');
        $this->__appConfig = Configure::read('Jukebox');
        $this->__httpSocket = new HttpSocket();
    }

    public function playClip($uid = null, $channel = 'Channel1', $playImmediately = true) {
        if(empty($uid))
            throw new Exception('UID is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'SemiAutomatic',
            'Command' => 'PlayNow',
            'PlayImmediately' => ($playImmediately ? 'YES' : 'NO'),
            'UID' => $uid,
            'Channel' => $channel
        ));
    }

    public function playMarketingClip($fileName = null, $duration = null) {
        if(empty($fileName))
            throw new Exception('File name is required');

        if(empty($duration))
            throw new Exception('Duration is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'Marketing',
            'Command' => 'PlayNow',
            'ClipFilename' => $fileName,
            'Duration' => $duration
        ));
    }

    public function resetMarketing() {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'Marketing',
            'Command' => 'PlayNow'
        ));
    }

    public function setVolume($level = null, $channel = 'Channel1') {
        if(empty($level))
            throw new Exception('Audio level is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'PlayerControl',
            'Command' => 'SetVolume',
            'Value' => $level,
            'Channel' => $channel
        ));
    }

    public function skip($value = 1) {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'PlayerControl',
            'Command' => 'Skip',
            'Value' => $value
        ));
    }

    public function refreshMarketing() {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['silvaIp'] . ':' . $this->__appConfig['services']['silvaPort'] . '/mailslot', array(
            'FunctionName' => 'Marketing',
            'Command' => 'Schedule',
            'ReadPages' => 'Yes',
            'ReadSpecialPage' => 'No',
            'ReadTickers' => 'No',
            'Clear' => 'Yes',
            'Dummy' => 1
        ));
    }
}