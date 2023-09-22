<?php
App::uses('AppController', 'Controller');

/**
 * SilvaPlaylistController Controller
 *
 * Components
 * @property MusicSchedulerComponent $MusicScheduler
 * @property SilvaComponent $Silva
 */
class SilvaPlaylistController extends AppController {
    public $components = array('RequestHandler', 'MusicScheduler', 'Silva');
    public $uses = false;

    public function enable() {
        $result = $this->MusicScheduler->enablePriorityPlaylist();

        if($result == false)
            throw new Exception('Could not enable priority playlist');

        $this->set('data', array('success' => ($result == true)));
        $this->set('_serialize', array('data'));
    }

    public function add_tracks() {
        $data = $this->request->input('json_decode', true);

        $added = array();
        $errors = false;
        if(!empty($data)) {
            $cnt = 1;
            foreach($data as &$item) {
                if($cnt > 1) usleep(500000);
                $result = $this->MusicScheduler->playlistInsert($item['uid'], $item['position']);
                if($result != true) {
                    $errors = true;
                } else {
                    $added[] = $item;
                    $cnt++;
                }
            }

            #$this->Silva->skip();
        }

        $this->set('data', array('success' => ($errors == false), 'added' => $added));
        $this->set('_serialize', array('data'));
    }
}