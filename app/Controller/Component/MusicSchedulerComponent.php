<?php
App::uses('HttpSocket', 'Network/Http');

class MusicSchedulerComponent extends Component {
    private $__appConfig;
    private $__httpSocket;

    public function initialize(Controller $controller) {
        Configure::load('jukebox');
        $this->__appConfig = Configure::read('Jukebox');
        $this->__httpSocket = new HttpSocket();
    }

    public function enablePriorityPlaylist($channel = 'Channel1') {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Enable',
            'Channel' => $channel
        ));
    }

    public function disablePriorityPlaylist($channel = 'Channel1') {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Disable',
            'Channel' => $channel
        ));
    }

    public function playlistReconstruct($channel = 'Channel1') {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Reconstruct',
            'Channel' => $channel
        ));
    }

    public function playlistAdd($uid= null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Add',
            'UID' => $uid,
            'Channel' => $channel
        ));
    }

    public function playlistInsert($uid= null, $position = null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        if(!isset($position))
            throw new Exception('Position is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Insert',
            'UID' => $uid,
            'POSITION' => $position,
            'Channel' => $channel
        ));
    }

    public function playlistMoveTo($uid= null, $position = null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        if(empty($position))
            throw new Exception('Position is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Move',
            'UID' => $uid,
            'POSITION' => $position,
            'Channel' => $channel
        ));
    }

    public function playlistMoveUp($uid= null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'MoveUp',
            'UID' => $uid,
            'Channel' => $channel
        ));
    }

    public function playlistMoveDown($uid= null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'MoveDown',
            'UID' => $uid,
            'Channel' => $channel
        ));
    }

    public function playlistDelete($uid= null, $channel = 'Channel1') {
        if(empty($uid))
            throw new Exception('UID is required');

        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Delete',
            'UID' => $uid,
            'Channel' => $channel
        ));
    }

    public function playlistClear($channel = 'Channel1') {
        return $this->__httpSocket->get('http://' . $this->__appConfig['services']['musicSchedulerIp'] . ':' . $this->__appConfig['services']['musicSchedulerPort'] . '/mailslot', array(
            'FunctionName' => 'PriorityPlaylist',
            'Command' => 'Clear',
            'Channel' => $channel
        ));
    }
}