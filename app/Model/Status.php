<?php
App::uses('AppModel', 'Model');

class Status extends AppModel {
    public $useTable = 'status';

    var $belongsTo = array(
        'Track' => array(
            'className' => 'Track',
            'foreignKey' => 'current_track_id'
        )
    );
}