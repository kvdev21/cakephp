<?php
App::uses('AppModel', 'Model');

class Track extends AppModel {
	var $name = 'Track';
	var $displayField = 'track_name';
    
    var $actsAs = array('Containable', 'Search.Searchable'/*, 'Habtamable'*/);
    
    var $filterArgs = array(
        array('name' => 'text', 'type' => 'query', 'method' => 'processText', 'field' => '?'),
        array('name' => 'artist', 'type' => 'value', 'field' => 'Track.artist_id'),
        array('name' => 'year', 'type' => 'value', 'field' => 'Track.year'),
        array('name' => 'tags', 'type' => 'subquery', 'method' => 'findByTags', 'field' => 'Track.id'),
        array('name' => 'tagId', 'type' => 'subquery', 'method' => 'findByTagIds', 'field' => 'Track.id')
    );

    #var $order = 'AddedOnConfigDate.date DESC';
    
    /*function processSmartFilters($data = array()) {
        if(!empty($data['sfid'])) {
            $smartFilter = $this->ConfigDateTrack->MusicEntityConfigDate->CompanyMusicEntity->Company->SmartFilter;
            $smartFilter->id = $data['sfid'];
            $smartFilters = $smartFilter->read();
            $smartFilterItems = new \Com\Kaleidovision\MusicPortal\SmartFilterItems;

            return $smartFilterItems->xml2sql($smartFilters['SmartFilter']['data']);
        } else {
            return false;
        }
    }*/

    function processText($data = array()) {
        return "Track.track_name LIKE \"%{$data['text']}%\" OR TrackArtist.name LIKE \"%{$data['text']}%\"";
    }

    function findByTags($data = array()) {
        $this->TrackTag->Behaviors->attach('Containable', array('autoFields' => false));
        $this->TrackTag->Behaviors->attach('Search.Searchable');
        $query = $this->TrackTag->getQuery('all', array(
            'conditions' => array(
                'Tag.title' => $data['tags']
            ),
            'contain' => array(
                'Tag'
            ),
            'fields' => array(
                'track_id'
            )
        ));

        return $query;
    }

    function findByTagIds($data = array()) {
        $this->TrackTag->Behaviors->attach('Containable', array('autoFields' => false));
        $this->TrackTag->Behaviors->attach('Search.Searchable');
        $query = $this->TrackTag->getQuery('all', array(
            'conditions' => array(
                'Tag.id' => $data['tagId']
            ),
            'contain' => array(
                'Tag'
            ),
            'fields' => array(
                'track_id'
            )
        ));

        return $query;
    }

	var $validate = array(
		'artist_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'genre_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'track_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'A track name must be entered'
			),
		),
		'bpm' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'BPM should be a number'
			),
		),
		'year' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'The year should be a number'
			),
		),
		'high' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'High chart position must be numeric'
			),
		),
		'length' => array(
			'time' => array(
				'rule' => array('time'),
				'message' => 'Invalid time format specified for track length'
			),
		),
		'file' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'A filename must be entered',
			),
		),
		'rating' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Rating must be a number'
			),
		),
	);

	var $belongsTo = array(
		'TrackArtist' => array(
			'className' => 'TrackArtist',
			'foreignKey' => 'artist_id'
		),
        'AddedOnConfigDate' => array(
			'className' => 'MusicEntityConfigDate',
			'foreignKey' => 'added_on_config_date_id'
        ),
        'Genre1' => array(
            'className' => 'Genre',
            'foreignKey' => "genre_1_id"
        ),
        'Genre2' => array(
            'className' => 'Genre',
            'foreignKey' => "genre_2_id"
        ),
        'Genre3' => array(
            'className' => 'Genre',
            'foreignKey' => "genre_3_id"
        )
	);

	var $hasAndBelongsToMany = array(
		'Playlist' => array(
			'className' => 'Playlist',
			'with' => 'PlaylistTrack',
			'foreignKey' => 'track_id',
			'associationForeignKey' => 'playlist_id',
			'unique' => true
		),
        'MusicEntityConfigDate' => array(
			'className' => 'MusicEntityConfigDate',
			'with' => 'ConfigDateTrack',
			'foreignKey' => 'track_id',
			'associationForeignKey' => 'music_entity_config_date_id',
			'unique' => true
		),
        'Tag' => array(
            'className' => 'Tag',
            'with' => 'TrackTag',
            'foreignKey' => 'track_id',
            'associationForeignKey' => 'tag_id',
            'unique' => true
        )
	);

    var $hasMany = array(
        'ConfigDateTrack' => array(
            'className' => 'ConfigDateTrack',
            'foreignKey' => 'track_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function beforeFind($query) {
        /*$configDate = $this->MusicEntityConfigDate->find('first', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $this->appConfig['configIndex']
            ),
            'contain' => false,
            'order' => array(
                'date' => 'desc'
            )
        ));*/

        /*if(empty($this->latestConfigDate['MusicEntityConfigDate']['id'])) return false;

        $query['joins'][] = array(
            'table' => 'config_date_tracks',
            'alias' => 'ConfigDateTrackInner',
            'type' => 'inner',
            'foreignKey' => false,
            'conditions' => array(
                'ConfigDateTrackInner.track_id = Track.id'
            )
        );*/

        /*$query['joins'][] = array(
            'table' => 'music_entity_config_dates',
            'alias' => 'MusicEntityConfigDateInner',
            'type' => 'inner',
            'foreignKey' => false,
            'conditions' => array(
                'MusicEntityConfigDateInner.id = ConfigDateTrackInner.music_entity_config_date_id'
            )
        );*/

        #$query['conditions']['ConfigDateTrackInner.music_entity_config_date_id'] = $this->latestConfigDate['MusicEntityConfigDate']['id'];

        /*$configDate = $this->MusicEntityConfigDate->find('first', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $this->appConfig['configIndex']
            ),
            'contain' => array(
                'ConfigDateTrack.track_id'
            ),
            'order' => array(
                'date' => 'desc'
            )
        ));*/

       /*$configDate = $this->MusicEntityConfigDate->find('first', array(
            'conditions' => array(
                'MusicEntityConfigDate.entity_id' => $this->appConfig['configIndex']
            ),
            'contain' => false,
            'order' => array(
                'date' => 'desc'
            )
        ));

        if(empty($configDate['MusicEntityConfigDate'])) return false;*/

        /*$trackIds = Set::classicExtract($configDate['ConfigDateTrack'], '{n}.track_id');

        $query['conditions'][get_class($this) . '.id'] = $trackIds;*/

        /*$query['contain'][]['ConfigDateTrack'] = array(
            'conditions' => array(
                'ConfigDateTrack.music_entity_config_date_id' => $configDate['MusicEntityConfigDate']['id']
            )
        );*/
        #$query['conditions']['ConfigDateTrack.music_entity_config_date_id'] = $configDate['MusicEntityConfigDate']['id'];
        #$query['conditions']['not']['ConfigDateTrack.id'] = null;

        /*debug($query);
        exit;*/

        return $query;
    }
    
    /*function beforeFind($queryData) {
        if(!PHP_CLI) {
            // Filter out tracks based on access control
            App::import('Component', 'Session');
            $session = new SessionComponent();
            $currentUser = array(
                'User' => $session->read('Auth.User')
            );

            App::import('Component', 'Acl');
            $acl = new AclComponent();

            if($acl->check($currentUser, 'Controller/Tracks', 'read')) {
                // All tracks
                // Do nothing
            } elseif($acl->check($currentUser, 'Controller/Tracks/Company', 'read')) {
                // Company tracks only

                // Non cached method
                $queryData['joins'][] = array(
                    'table' => 'config_date_tracks',
                    'alias' => 'ConfigDateTrackInner',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array(
                        'ConfigDateTrackInner.track_id = Track.id'
                    )
                );

                $queryData['joins'][] = array(
                    'table' => 'music_entity_config_dates',
                    'alias' => 'MusicEntityConfigDateInner',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array(
                        'MusicEntityConfigDateInner.id = ConfigDateTrackInner.music_entity_config_date_id'
                    )
                );

                $queryData['joins'][] = array(
                    'table' => 'company_music_entities',
                    'alias' => 'CompanyMusicEntityInner',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array(
                        'CompanyMusicEntityInner.id = MusicEntityConfigDateInner.company_music_entity_id'
                    )
                );

                // Get the current users company
                $user = $this->MusicEntityConfigDate->CompanyMusicEntity->Company->Venue->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $currentUser['User']['id']
                    ),
                    'contain' => array(
                        'Venue'
                    )
                ));

                if(!empty($user['Venue']['company_id'])) {
                    // AH 2013-02-12
                    #$queryData['conditions']['CompanyMusicEntityInner.company_id'] = $user['Venue']['company_id'];

                    $musicEntityIds = $this->MusicEntityConfigDate->CompanyMusicEntity->entitiesForCompany($user['Venue']['company_id']);


                    if(!empty($musicEntityIds)) {
                        $queryData['conditions']['CompanyMusicEntityInner.entity_id'] = $musicEntityIds;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                // No tracks
                return false;
            }
        }

        return $queryData;
    }*/
    
    function get($id) {
        $results = $this->find('first', array(
            'conditions' => array(
                'Track.id' => $id
            ),
            'contain' => array(
                'TrackArtist',
                'AddedOnConfigDate',
                'Genre1',
                'Genre2',
                'Genre3',
                'Playlist' => array(
                    'DayPart',
                    'WeekPart',
                ),
                'Playlist' => array(
                    'DayPart',
                    'WeekPart',
                    'User' => array(
                        'Venue'
                    ),
                    'PlaylistRating'
                ),
                'TrackReview' => array(
                    'User',
                    'order' => 'TrackReview.date_added DESC'
                ),
                'TrackRating'
            )
        ));
                
        // Get user_rating psuedo field for each playlist
        // by firing the afterFind on the Rateable behavior
        $results['Playlist'] = $this->Playlist->Behaviors->trigger($this->Playlist, 'afterFind', array('results' => $results['Playlist'], 'primary' => true), array('modParams' => true));
        
        return $results;
    }

    public function decrypt($idOrFilename = null) {
        $idOrFilename = (!isset($idOrFilename)) ? $this->id : $idOrFilename;

        if(is_numeric($idOrFilename)) {
            $track = $this->find('first', array(
                'conditions' => array(
                    'Track.id' => $idOrFilename
                )
            ));

            if(empty($track['Track']['file']))
                throw new Exception('Invalid track ID');

            $fileName = $track['Track']['file'];
        } else {
            $fileName = $idOrFilename;
        }

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $this->cleanOldDecryptedClips();

        if($fileName == '') {
            // Empty fileName means decrypt the holding clip (obviously!)
            $fileLocation = $this->appConfig['app']['holdingClipDir'] . DS . $this->appConfig['app']['holdingClipFilename'];
            $newFileName = str_replace('.mpe', '.mpg', $this->appConfig['app']['holdingClipFilename']);
            $decryptedFileLocation = $this->appConfig['app']['holdingClipDir'] . DS .  $newFileName;
            $newFileLocation = $this->appConfig['decryptedClipsDir'] . DS . $newFileName;
            $switch = '-m';
            $type = 'audio';
        } else {
            $ext = substr($fileName, -4, 4);

            switch($ext) {
                case '.m3e':
                    $fileLocation = $this->appConfig['app']['clipsDir'] . DS . 'mp3' . DS . $fileName;
                    $newFileName = str_replace('.m3e', '.mp3', $fileName);
                    $decryptedFileLocation = $this->appConfig['app']['clipsDir'] . DS . 'mp3' . DS . $newFileName;
                    $newFileLocation = $this->appConfig['decryptedClipsDir'] . DS . $newFileName;
                    $switch = '-3';
                    $type = 'audio';
                    break;
                case '.mpe':
                    $fileLocation = $this->appConfig['app']['clipsDir'] . DS . 'mpeg' . DS . $fileName;
                    $newFileName = str_replace('.mpe', '.mpg', $fileName);
                    $decryptedFileLocation = $this->appConfig['app']['clipsDir'] . DS . 'mpeg' . DS .  $newFileName;
                    $newFileLocation = $this->appConfig['decryptedClipsDir'] . DS . $newFileName;
                    $switch = '-m';
                    $type = 'video';
                    break;
                default:
                    throw new Exception('Valid filename was not specified');
                    break;
            }
        }

        if(!file_exists($newFileLocation)) {
            if(chdir($this->appConfig['app']['clipsDir'])) {
                $cmd = $this->appConfig['clixBinary'] . ' -d ' . $switch . ' -t "' . $fileLocation . '"';
                exec($cmd, $response);

                if(!rename($decryptedFileLocation, $newFileLocation)) {
                    throw new Exception('Could not move decrypted file');
                }
            }
        }

        return array('filename' => $newFileName, 'mrl' => 'file:///' . $newFileLocation, 'type' => $type);
    }

    public function cleanOldDecryptedClips() {
        if(is_dir($this->appConfig['decryptedClipsDir'])) {
            $files = scandir($this->appConfig['decryptedClipsDir']);

            $holdingClipFileName = str_replace('.mpe', '.mpg', $this->appConfig['app']['holdingClipFilename']);

            // Get upcoming playlist to exclude
            $upcomingFileNames = array();
            App::uses('HttpSocket', 'Network/Http');
            $http = new HttpSocket();
            if($response = $http->get('http://' . $this->appConfig['services']['musicSchedulerIp'] . ':' . $this->appConfig['services']['musicSchedulerHttpPort'] . '/playlist.xml')) {
                try {
                    $xml = new SimpleXMLElement($response);
                    foreach($xml->{'MEDIA-CLIP'} as $clip) {
                        $playClip = $clip->{'PLAY'}->{'PLAY-CLIP'};
                        $upcomingFileNames[] = (string) str_replace(array('.mpe', '.m3e'), array('.mpg', '.mp3'), $playClip['FILE-NAME']);
                    }
                } catch(Exception $e) {
                    $this->log('Exception while getting music scheduler XML: ' . $e->getMessage(), 'clean_up');
                }
            }

            if(!empty($files)) {
                foreach($files as $file) {
                    if($file === '.' || $file === '..') continue;
                    if($file == $holdingClipFileName) continue;
                    if(in_array($file, $upcomingFileNames)) continue;

                    unlink($this->appConfig['decryptedClipsDir'] . DS . $file);
                }
            }
        }
    }

    public function generateWaveform($id = null, $width = 5000, $height = 55, $cleanUp = true) {
        $id = (!$id) ? $this->id : $id;

        if(empty($id))
            throw new Exception('No track ID specified');

        $track = $this->find('first', array(
            'conditions' => array(
                'Track.id' => $id
            )
        ));

        #$this->log($track, 'debug');

        if(empty($track['Track']))
            throw new Exception('Invalid track ID');

        // Boilerplate
        $path = App::path('Lib');
        $binPath = $path[0] . 'Com' . DS . 'Kaleidovision' . DS . 'Shell' . DS . 'bin';

        #$this->log($binPath, 'debug');

        App::import('Shell', 'FfmpegProgram');
        App::import('Shell', 'WaveformProgram');

        $ffmpeg = new \Com\Kaleidovision\Shell\FfmpegProgram($binPath . DS . 'ffmpeg' . DS . 'ffmpeg.exe');

        #$ffmpeg->debug = true;

        $waveform = new \Com\Kaleidovision\Shell\WaveformProgram($binPath . DS . 'waveform' . DS . 'waveform.exe');

        // 1. Decrypt (if required)
        $result = $this->decrypt($track['Track']['file']);

        // 2. Convert to WAV
        $ffmpeg->inputFile = $this->appConfig['decryptedClipsDir'] . DS . $result['filename'];
        $info = pathinfo($ffmpeg->inputFile);
        $ffmpeg->outputFile = $this->appConfig['waveformTmpDir'] . DS . $info['filename'] . '.wav';

        if(!$ffmpeg->extractWav())
           throw new Exception('Could not extract WAV from original clip');

        if($cleanUp === true) {
            if(file_exists($ffmpeg->inputFile))
                unlink($ffmpeg->inputFile);
        } elseif ($cleanUp == 'audio') {
            $ext = substr($ffmpeg->inputFile, -4, 4);

            if($ext == '.mp3' && file_exists($ffmpeg->inputFile))
                unlink($ffmpeg->inputFile);
        }

        // 3. Generate waveform
        $waveform
            //->setWidth(32767)
            ->setWidth($width)
            ->setHeight($height)
            ->setColorBg('000000')
            ->setColorOuter('278DB5')
            ->setColorCenter('09FCFA');

        $waveform->run($ffmpeg->outputFile, $this->appConfig['waveformDir'] . DS . $track['Track']['uid'] . '.png');

        if(!empty($cleanUp)) {
            if(file_exists($ffmpeg->outputFile))
                unlink($ffmpeg->outputFile);
        }
    }
}