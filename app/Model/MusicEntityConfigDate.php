<?php
App::uses('AppModel', 'Model');

class MusicEntityConfigDate extends AppModel {
	var $displayField = 'title';
	var $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'company_music_entity_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    
	var $hasAndBelongsToMany = array(
		'Track' => array(
			'className' => 'Track',
			'with' => 'ConfigDateTrack',
			'foreignKey' => 'music_entity_config_date_id',
			'associationForeignKey' => 'track_id',
			'unique' => true
		)
    );

    var $hasMany = array(
        'ConfigDateTrack' => array(
            'className' => 'ConfigDateTrack',
            'foreignKey' => 'music_entity_config_date_id',
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
}