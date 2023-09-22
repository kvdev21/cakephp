<?php
App::uses('AppController', 'Controller');

/**
 * VideoController Controller
 *
 * Components
 */
class VideoController extends AppController {
    public $components = array('RequestHandler');
    public $uses = array('Status', 'Track');

    public function get_current() {
        $filename =  '';
        $offset = 0;
        $trackStartTime = 0;

        $status = $this->Status->find('first', array(
            'contain' => array(
                'Track' => array(
                    'TrackArtist'
                )
            )
        ));

        $now = round(microtime(true) * 1000);

        if(!empty($status)) {
            #if($status['Status']['current_track_type'] == 'video') {

                #$now = time() * 1000;
                $trackStart = $status['Status']['current_track_start_time'];

                $split = explode(':', $status['Track']['length']);
                #$trackLength = new DateInterval('PT' . (int)$split[0] . 'H' . (int)$split[1] . 'M'. (int)$split[2] . 'S');
                $trackLength = ($split[0] * 60 * 60) + ($split[1] * 60) + $split[2];
                $trackEnd = ($trackLength * 1000) + $status['Status']['current_track_start_time'];
                //$trackEnd->add($trackLength);

                if($trackEnd > $now) {
                    #$filename = $status['Track']['file'];
                    $offset = ($now - $trackStart);
                    $trackStartTime = $trackStart;
                }
            #}

            if($status['Status']['current_track_type'] == 'video') {
                $filename = $status['Track']['file'];
            }
        }

        $finished = (($trackStartTime + $offset) > $now);

        $this->set('data', array('filename' => $filename, 'offset' => $offset, 'status' => $status, 'start' => $trackStartTime, 'finished' => $finished));
        $this->set('_serialize', array('data'));
    }

    public function check_for_update() {
        $update = false;

        $status = $this->Status->find('first', array(
            'contain' => false
        ));

        if(!empty($status)) {
            if(($status['Status']['current_track_start_time'] / 1000) > strtotime($status['Status']['last_video_update'])) {
                $this->Status->id = $status['Status']['id'];
                $this->Status->saveField('last_video_update', date('Y-m-d H:i:s'));
                $update = true;
            }
        }

        $this->set('data', array('update' => $update));
        $this->set('_serialize', array('data'));
    }

    public function decrypt() {
        $data = $this->request->input('json_decode', true);

        if(!isset($data['filename']))
            throw new Exception('Valid filename was not specified');

        $result = $this->Track->decrypt($data['filename']);

        $this->set('data', $result);
        #$this->set('data', array('filename' => $newFileName, 'mrl' => $this->appConfig['url'] . '/files/decrypted-clips/' . urlencode($newFileName), 'type' => $type));
        #$this->set('data', array('filename' => $newFileName, 'mrl' => str_replace('/', "\\", $newFileLocation)));
        $this->set('_serialize', array('data'));
    }
}