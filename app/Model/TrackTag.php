<?php
class TrackTag extends AppModel {
    var $belongsTo = array(
        'Track' => array(
            'className' => 'Track',
            'foreignKey' => 'track_id'
        ),
        'Tag' => array(
            'className' => 'Tag',
            'foreignKey' => 'tag_id'
        )
    );
}