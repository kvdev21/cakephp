<?php
App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * Tracks Controller
 *
 * @property Track $Track
 */
class TracksController extends AppController {
    public $components = array('RequestHandler', 'Search.Prg');
    public $uses = array('Track', 'MusicEntityConfigDate');

    /**
     * index method
     *
     * @return void
     */
	public function index() {
        $this->Prg->commonProcess();

        $idsOnly = (!empty($this->request->named['ids-only']) && $this->request->named['ids-only'] == 'true');

        $latestConfigDate = $this->MusicEntityConfigDate->find('first', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $this->appConfig['configIndex']
            ),
            'contain' => false,
            'order' => array(
                'date' => 'desc'
            )
        ));

        if(empty($latestConfigDate['MusicEntityConfigDate']['id']))
            throw new Exception('Cannot find latest config date in DB');

        if($idsOnly) {
            $contain = array(
                'TrackArtist.id'
            );
            $fields = array(
                'Track.id'
            );
        } else {
            $contain = array(
                'TrackArtist',
                /*'Genre1',
                'Genre2',
                'Genre3',
                'Tag'*/
            );
            $fields = null;
        }

        $conditions = $this->Track->parseCriteria($this->Prg->parsedParams());
        $conditions[] = 'ConfigDateTrack.music_entity_config_date_id = ' . $latestConfigDate['MusicEntityConfigDate']['id'];

        $tracks = $this->Track->find('all', array(
            'order' => array(
                'TrackArtist.name' => 'ASC',
                'Track.track_name' => 'ASC',
                'Track.id' => 'ASC'
            ),
            'contain' => $contain,
            'joins' => array(
                array(
                    'table' => 'config_date_tracks',
                    'alias' => 'ConfigDateTrack',
                    'type' => 'inner',
                    'conditions' => array(
                        'ConfigDateTrack.track_id = Track.id'
                    )
                )
            ),
            'fields' => $fields,
            'group' => 'Track.id',
            'conditions' => $conditions/*,
            'limit' => 100*/
        ));

        if(!empty($tracks)) {
            // Limit to 1000 tracks
            /*$i = 0;
            foreach($tracks as $key => &$track) {
                if($i > 1000)
                    unset($tracks[$key]);

                $i++;
            }
            unset($key, $track);*/

            if($idsOnly) {
                $this->set('data', Set::classicExtract($tracks, '{n}.Track.id'));
            } else {
                foreach($tracks as &$track) {
                    $track['Track']['TrackArtist'] = &$track['TrackArtist'];
                    $track['Track']['Genre1'] = &$track['Genre1'];
                    $track['Track']['Genre2'] = &$track['Genre2'];
                    $track['Track']['Genre3'] = &$track['Genre3'];
                    #$track['Track']['Tag'] = &$track['Tag'];
                }

                $this->set('data', Set::classicExtract($tracks, '{n}.Track'));
            }
        }

        $this->set('_serialize', array('data'));

        /*$this->autoRender = $this->layout = false;
        debug($tracks);*/

		//$this->set('data', Set::classicExtract($tweets, '{n}.Tweet'));*/
	}

    /*public function find() {
        $this->Prg->commonProcess();
        $this->Paginator->settings['conditions'] = $this->Article->parseCriteria($this->Prg->parsedParams());
        $this->set('articles', $this->Paginator->paginate());
    }*/

    /**
     * waveform method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
	public function waveform($id = null, $width = 5000, $height = 55) {
        $success = true;

        $track = $this->Track->find('first', array(
            'conditions' => array(
                'Track.id' => $id
            )
        ));

        if(empty($track['Track']['uid'])) {
            $success = false;
        }

        if(!file_exists($this->appConfig['waveformDir'] . DS . $track['Track']['uid'] . '.png')) {
            $this->Track->generateWaveform($id, $width, $height, 'audio');
        }

        $this->set('data', array('success' => $success));
        $this->set('_serialize', array('data'));
	}

    /**
     * generate_waveforms method
     *
     * @throws NotFoundException
     * @return void
     */
    public function generate_waveforms($width = 5000, $height = 55) {
        /*$latestConfigDate = $this->MusicEntityConfigDate->find('first', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $this->appConfig['configIndex']
            ),
            'contain' => false,
            'order' => array(
                'date' => 'desc'
            )
        ));

        if(empty($latestConfigDate['MusicEntityConfigDate']['id'])) {
            $msg = 'Cannot find latest config date in DB';
            $this->log($msg, 'generate_waveforms');
            throw new Exception($msg);
        }

        $tracks = $this->Track->find('all', array(
            'order' => array(
                'Track.id' => 'ASC'
            ),
            'contain' => false,
            'joins' => array(
                array(
                    'table' => 'config_date_tracks',
                    'alias' => 'ConfigDateTrack',
                    'type' => 'inner',
                    'conditions' => array(
                        'ConfigDateTrack.track_id = Track.id'
                    )
                )
            ),
            'fields' => null,
            'group' => 'Track.id',
            'conditions' => array(
                'ConfigDateTrack.music_entity_config_date_id = ' . $latestConfigDate['MusicEntityConfigDate']['id']
            )
        ));

        if(!empty($tracks)) {
            // Generate waveforms

            set_time_limit(0);
            ini_set('memory_limit', '1024M');
            session_write_close();

            foreach($tracks as $track) {
                if(!file_exists($this->appConfig['waveformDir'] . DS . $track['Track']['uid'] . '.png')) {
                    try {
                        $this->Track->generateWaveform($track['Track']['id'], $width, $height, true);
                    } catch (Exception $e) {
                        $this->log('Could not generate waveform for track ' . $track['Track']['uid'] . ': ' . $e->getMessage(), 'generate_waveforms');
                    }
                }
            }
        }
		*/

        $this->set('data', array('success' => true));
        $this->set('_serialize', array('data'));
    }
}