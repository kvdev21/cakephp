<?php
App::uses('AppController', 'Controller');

/**
 * MusicScheduler Controller
 */
class SilvaController extends AppController {
    public $components = array('RequestHandler', 'MusicScheduler');
    public $uses = array('Status', 'Track');

    public function register_play_clip() {
        if(empty($this->request->named['type']) || empty($this->request->named['uid']) || !isset($this->request->named['cue-in']) || empty($this->request->named['message-time'])) {
            throw new Exception('Not all required params were supplied');
        }

        $type = $this->request->named['type'];
        $uid = $this->request->named['uid'];
        $startTime = strtotime($this->request->named['message-time']);

        // Milliseconds
        $startTime *= 1000;
        $mils = (int) substr($this->request->named['message-time'], strpos($this->request->named['message-time'], '.')+1, 3);

        $startTime += $mils;

        $startTime -= $this->request->named['cue-in'];

        $track = $this->Track->find('first', array(
            'conditions' => array(
                'Track.uid' => $uid
            )
        ));

        if(empty($track['Track']['id']))
            throw new Exception('Track with UID ' . $uid . ' does not exist in jukebox database');

        $status = $this->Status->find('first');

        if(empty($status['Status']['id'])) {
            $this->Status->create();
        } else {
            $this->Status->id = $status['Status']['id'];
        }

        $data = array(
            'current_track_id' => $track['Track']['id'],
            'current_track_type' => $type,
            'current_track_start_time' => $startTime
        );

        #$this->log($data, 'debug');

        $this->Status->save($data);

        $this->set('data', array('ok' => 'ok'));
        $this->set('_serialize', array('data'));
    }
}