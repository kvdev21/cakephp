<?php
App::uses('AppModel', 'Model');

class Playlist extends AppModel {
	var $displayField = 'title';
    
    var $actsAs = array('Search.Searchable');
    
    var $filterArgs = array(
        array('name' => 'name', 'type' => 'like', 'field' => 'Playlist.title'),
        array('name' => 'site', 'type' => 'value', 'field' => 'User.venue_id'),
        array('name' => 'author', 'type' => 'like', 'field' => 'Playlist.author'),
        array('name' => 'date', 'type' => 'like', 'field' => 'Playlist.date_added'),
        array('name' => 'daypart', 'type' => 'value', 'field' => 'Playlist.date_part_id'),
        array('name' => 'weekpart', 'type' => 'value', 'field' => 'Playlist.week_part_id')
    );
    
	var $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'A title must be entered for the playlist'
			),
		),
		'day_part_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'week_part_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'times_played' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'file' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'A filename must be specified'
			),
		),
		'rating' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);
    
	var $hasMany = array(
		'PlaylistTrack' => array(
			'className' => 'PlaylistTrack',
			'foreignKey' => 'playlist_id',
			'dependent' => false
		)
	);
    
	var $hasAndBelongsToMany = array(
		'Track' => array(
			'className' => 'Track',
			'with' => 'PlaylistTrack',
			'foreignKey' => 'playlist_id',
			'associationForeignKey' => 'track_id',
			'unique' => true
		)
    );
    
    function get($id) {
        $results = $this->find('first', array(
            'conditions' => array(
                'Playlist.id' => $id
            ),
            'contain' => array(
                'DayPart',
                'WeekPart',
                /*'Track' => array(
                    'TrackArtist',
                    'TrackRating',
                    'AddedOnConfigDate',
                    'order' => array(
                        'PlaylistTrack.position ASC'
                    )
                ),*/
                'PlaylistReview' => array(
                    'User',
                    'order' => 'PlaylistReview.date_added DESC'
                ),
                'User' => array(
                    'Venue'
                ),
                'PlaylistRating'
            )
        ));
        
        // Manually add tracks to data (to trigger the beforeFind ACL checks)
        $results['Track'] = $this->PlaylistTrack->find('all', array(
            'conditions' => array(
                'PlaylistTrack.playlist_id' => $id
            ),
            'contain' => array(
                'Track' => array(
                    'TrackArtist',
                    'TrackRating',
                    'Genre1',
                    'Genre2',
                    'Genre3',
                    'AddedOnConfigDate'
                )
            ),
            'order' => array(
                'PlaylistTrack.position ASC'
            )
        ));
        
        /*
        // Manually add tracks to data (to trigger the beforeFind ACL checks)
        $results['Track'] = $this->Track->find('all', array(
            'joins' => array(
                array(
                    'table' => 'playlist_tracks',
                    'alias' => 'PlaylistTrackInner',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array(
                        'PlaylistTrackInner.track_id = Track.id'
                    )
                )
            ),
            'conditions' => array(
                'PlaylistTrackInner.playlist_id' => $id
            ),
            'contain' => array(
                'Playlist' => array(
                    'conditions' => array(
                        'Playlist.id' => $id
                    )
                ),
                'TrackArtist',
                'TrackRating',
            ),
            'order' => array(
                'PlaylistTrackInner.position ASC'
            )
        ));*/
        
        /*if(!empty($results['Track'])) {
            foreach($results['Track'] as $key => $track) {
                
            }
        }*/
        
        // Get user_rating psuedo field for each track
        // by firing the afterFind on the Rateable behavior
        #$results['Track'] = $this->Track->Behaviors->trigger(&$this->Track, 'afterFind', array('results' => $results['Track'], 'primary' => true), array('modParams' => true));
        // (no longer required)
        
        return $results;
    }
    
    function beforeSave() {
        /*if(!PHP_CLI) {
            App::import('Component', 'Session');
            $session = new SessionComponent();
            $user = $session->read('Auth.User');

            $this->data['Playlist']['user_id'] = $user['id'];
        } else {
            $this->data['Playlist']['user_id'] = 0;
        }*/

        $this->data['Playlist']['user_id'] = 0;
        
        return true;
    }
}
?>