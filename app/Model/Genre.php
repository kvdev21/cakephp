<?php
App::uses('AppModel', 'Model');

class Genre extends AppModel {
	var $name = 'Genre';
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
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'TrackGenre1' => array(
			'className' => 'Track',
			'foreignKey' => 'genre_1_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TrackGenre2' => array(
			'className' => 'Track',
			'foreignKey' => 'genre_2_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TrackGenre3' => array(
			'className' => 'Track',
			'foreignKey' => 'genre_3_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}