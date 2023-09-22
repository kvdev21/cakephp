<?php
App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

#App::import('Data', 'Data');
App::import('MusicPortal', 'ConfigDate');

/**
 * Jukebox Controller
 *
 * @property MusicEntityConfigDate $MusicEntityConfigDate
 */
class JukeboxController extends AppController {
    public $components = array('RequestHandler', 'MusicScheduler', 'Silva');
    public $uses = array('MusicEntityConfigDate', 'ConfigDateTrack', 'Track', 'TrackArtist', 'TrackTag', 'Tag');

    public function beforeFilter() {
        parent::beforeFilter();

        $this->layout = 'dashboard';
    }

    public function get_latest_config_dates() {
        $this->autoRender = $this->layout = false;

        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        session_write_close();

        $configDate = new \Com\Kaleidovision\MusicPortal\ConfigDate;
        $configDate->loadFromDirectory($this->appConfig['musicConfigPath']);
        $configDate->channel = 'Channel1';
        $configDate->entityId = $this->appConfig['configIndex'];

        $cnt = $this->MusicEntityConfigDate->find('count', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $configDate->entityId,
                'MusicEntityConfigDate.channel' => $configDate->channel,
                'MusicEntityConfigDate.date' => $configDate->date
            )
        ));

        if($cnt > 0) return;

        $this->log('Importing config date: ' . $configDate->date, 'get_latest_config_dates');

        $data = $configDate->save();

        #$configDate->save($this->MusicEntityConfigDate);

        $this->MusicEntityConfigDate->create();
        if($this->MusicEntityConfigDate->save($data['MusicEntityConfigDate'])) {
            if(!empty($data['Track'])) {
                $trackTagData = array();
                $tags = $this->Tag->find('list');
                foreach($data['Track'] as $track) {
                    // Save Track
                    $dbTrack = $this->Track->find('first', array(
                        'conditions' => array(
                            'Track.uid' => $track['uid']
                        )
                    ));

                    if(!empty($dbTrack)) {
                        $this->Track->id = $dbTrack['Track']['id'];
                    } else {
                        $this->Track->create();
                        $this->Track->save($track);
                        $track['added_on_config_date_id'] = $this->MusicEntityConfigDate->id;
                    }

                    // Save ConfigDateTrack
                    $this->ConfigDateTrack->create();
                    $this->ConfigDateTrack->save(array(
                        'music_entity_config_date_id' => $this->MusicEntityConfigDate->id,
                        'track_id' => $this->Track->id
                    ));

                    // Save Artist
                    $artist = $this->TrackArtist->find('first', array(
                        'conditions' => array(
                            'name' => $track['TrackArtist']['name']
                        ),
                        'contain' => false
                    ));

                    if(!empty($artist)) {
                        $this->TrackArtist->id = $artist['TrackArtist']['id'];
                    } else {
                        $this->TrackArtist->create();
                        $this->TrackArtist->save($track['TrackArtist']);
                    }

                    $this->Track->saveField('artist_id',  $this->TrackArtist->id);

                    if(!empty($track['TrackTag'])) {
                        foreach($track['TrackTag'] as &$trackTag) {
                            if(in_array($trackTag['Tag']['title'], $tags)) {
                                $this->Tag->id = array_search($trackTag['Tag']['title'], $tags);
                            } else {
                                // Tag
                                $tag = $this->Tag->find('first', array(
                                    'conditions' => array(
                                        'title' => $trackTag['Tag']['title']
                                    ),
                                    'contain' => false
                                ));

                                if(!empty($tag)) {
                                    $this->Tag->id = $tag['Tag']['id'];
                                } else {
                                    $this->Tag->create();
                                    $this->Tag->save($trackTag['Tag']);
                                }

                                $tags[$this->Tag->id] = $trackTag['Tag']['title'];
                            }

                            // Track Tag
                            /*$trackTagDb = $this->TrackTag->find('first', array(
                                'conditions' => array(
                                    'track_id' => $this->Track->id,
                                    'tag_id' => $this->Tag->id
                                ),
                                'contain' => false
                            ));

                            if(empty($trackTagDb)) {*/
                                /*$this->TrackTag->create();
                                $this->TrackTag->save(array(
                                    'track_id' => $this->Track->id,
                                    'tag_id' => $this->Tag->id
                                ));*/
                            /*}*/

                            $trackTagData[] = array(
                                'track_id' => $this->Track->id,
                                'tag_id' => $this->Tag->id
                            );
                        }
                    }
                }

                if(!empty($trackTagData)) {
                    $this->TrackTag->saveAll($trackTagData);
                }
            }
        }

        $this->log('Done', 'get_latest_config_dates');

        unset($cnt, $configDate, $musicConfigDateDir);
    }

    /**
     * update method
     *
     * @return void
     */
    public function update() {
    }

    /**
     * index method
     *
     * @return void
     */
	public function index() {
	}
}