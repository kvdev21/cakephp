<?php
/**
 * This file contains classes which allow playlist
 * data to be stored and manipulated
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.Playlist
 */

namespace Com\Kaleidovision\MusicPortal;

require_once('Data.php');
require_once('AutoUpdate.php');

// Load configuration
require_once('Configuration.php');

/**
 * Track Artist
 * @package TrackArtist
 */
class TrackArtist extends Data {
    public $name = '';

    /**
     * Creates a track artist
     * @param string $name Name of artist
     */
    public function __construct($name = '') {
        parent::__construct();

        $this->name = $name;
    }

    protected function _importDom() {
        parent::_importDom();

        if ($nodes = $this->_xml->getElementsByTagName('song')) {
            if ($node = $node->item(0)->attributes->getNamedItem('arti'))
                $this->name = $node->nodeValue;
        }
    }

    protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');
        $track = $this->_xml->appendChild($this->_xml->createElement('song'));
        $track->appendChild($this->_xml->createAttribute('arti'))->appendChild($this->_xml->createTextNode($this->name));

        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

        // Fill track artist details
        $this->id = (!empty($this->_data['TrackArtist']['id'])) ? (int)$this->_data['TrackArtist']['id'] : 0;
        $this->name = (!empty($this->_data['TrackArtist']['name'])) ? (string)$this->_data['TrackArtist']['name'] : '';
    }

    protected function _exportArray() {
        $this->_data = array(
            'TrackArtist' => array(
                'name' => $this->name
            )
        );

        if (!empty($this->id)) $this->_data['TrackArtist']['id'] = $this->id;

        parent::_exportArray();
    }
}

/**
 * Track Tag
 * @package TrackTag
 */
class TrackTag extends Data {
    public $title = '';

    /**
     * Creates a track artist
     * @param string $name Name of artist
     */
    public function __construct($title = '') {
        parent::__construct();

        $this->title = str_replace('$', '', $title);
    }

    protected function _importDom() {
        parent::_importDom();

        // Tags are not part of Silva playlists
    }

    protected function _exportDom() {
        // Tags are not part of Silva playlists

        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

        // Fill track artist details
        $this->id = (!empty($this->_data['TrackTag']['id'])) ? (int)$this->_data['TrackTag']['id'] : 0;
        $this->tagId = (!empty($this->_data['TrackTag']['Tag']['id'])) ? (int)$this->_data['TrackTag']['Tag']['id'] : 0;
        $this->title = (!empty($this->_data['TrackTag']['Tag']['title'])) ? (string)$this->_data['TrackTag']['Tag']['title'] : '';
    }

    protected function _exportArray() {
        $this->_data = array(
            'TrackTag' => array(
                'Tag' => array(
                    'title' => $this->title
                )
            )
        );

        if (!empty($this->id)) $this->_data['TrackTag']['id'] = $this->id;
        if (!empty($this->tagId)) $this->_data['TrackTag']['Tag']['id'] = $this->tagId;

        parent::_exportArray();
    }
}

/**
 * Track class
 * @package Track
 */
class Track extends Data {
    public $trackArtist;
    public $trackName = '';
    public $bpm = 0;
    public $year = 0;
    // TODO: Make a "Genre" object
    public $genre1 = 0;
    public $genre2 = 0;
    public $genre3 = 0;
    public $high = 0;
    public $length = '00:00:00';
    public $file = '';
    public $rating = 0.0000;
    public $reviewCount = 0;
    public $uid = '';
    public $startDate = '1899-12-31';
    public $endDate = '1899-12-31';
    public $startTime = '00:00';
    public $finishTime = '00:00';
    public $dow = 0;
    public $uids;
    public $tags;

    /* format yyyymmdd as yyyy-mm-dd */
    protected function _formatDateString($source) {
        if (strlen($source) >= 8) {
            $resYear = substr($source, 0, 4);
            $resMonth = substr($source, 4, 2);
            $resDay = substr($source, 6, 2);
            return $resYear . '-' . $resMonth . '-' . $resDay;
        }
        else
            return '1899-12-31';
    }

    /* format hhmm as hh:mm */
    public function _formatTimeString($source) {
        if (strlen($source) == 4)
            return substr($source, 0, 2) . ':' . substr($source, 2, 2);
        else
            return '00:00';
    }

    protected function _importDom() {
        parent::_importDom();

        if ($nodes = $this->_xml->getElementsByTagName('song')) {
            if ($attributes = $nodes->item(0)->attributes) {
                if ($node = $attributes->getNamedItem('titl'))
                    $this->trackName = $node->nodeValue;
                if ($node = $attributes->getNamedItem('bpmt'))
                    $this->bpm = $node->nodeValue;
                if ($node = $attributes->getNamedItem('year'))
                    $this->year = $node->nodeValue;
                if ($node = $attributes->getNamedItem('gnr1'))
                    $this->genre1 = $node->nodeValue + 1;
                if ($node = $attributes->getNamedItem('gnr2'))
                    $this->genre2 = $node->nodeValue + 1;
                if ($node = $attributes->getNamedItem('gnr3'))
                    $this->genre3 = $node->nodeValue + 1;
                if ($node = $attributes->getNamedItem('time'))
                    $this->length = date('H:i:s', strtotime($node->nodeValue));
                if ($node = $attributes->getNamedItem('fnam'))
                    $this->file = basename(str_replace('\\', '/', $node->nodeValue));

                if ($node = $attributes->getNamedItem('arti'))
                    $this->trackArtist = new TrackArtist($node->nodeValue);
            }
        }
    }

    protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');
        $track = $this->_xml->appendChild($this->_xml->createElement('song'));

        $track->appendChild($this->_xml->createAttribute('titl'))->appendChild($this->_xml->createTextNode($this->trackName));
        $track->appendChild($this->_xml->createAttribute('bpmt'))->appendChild($this->_xml->createTextNode($this->bpm));
        $track->appendChild($this->_xml->createAttribute('year'))->appendChild($this->_xml->createTextNode($this->year));
        $track->appendChild($this->_xml->createAttribute('gnr1'))->appendChild($this->_xml->createTextNode($this->genre1 - 1));
        $track->appendChild($this->_xml->createAttribute('gnr2'))->appendChild($this->_xml->createTextNode($this->genre2 - 1));
        $track->appendChild($this->_xml->createAttribute('gnr3'))->appendChild($this->_xml->createTextNode($this->genre3 - 1));
        $track->appendChild($this->_xml->createAttribute('time'))->appendChild($this->_xml->createTextNode($this->length));
        $track->appendChild($this->_xml->createAttribute('fnam'))->appendChild($this->_xml->createTextNode($this->file));

        if (!empty($this->trackArtist))
            $track->appendChild($this->_xml->createAttribute('arti'))->appendChild($this->_xml->createTextNode($this->trackArtist->name));

        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

        // Fill track details
        $this->id = (!empty($this->_data['Track']['Track']['id'])) ? (int)$this->_data['Track']['Track']['id'] : 0;
        $this->trackName = (!empty($this->_data['Track']['Track']['track_name']))
                ? (string)$this->_data['Track']['Track']['track_name'] : '';
        $this->bpm = (!empty($this->_data['Track']['Track']['bpm'])) ? (int)$this->_data['Track']['Track']['bpm'] : 0;
        $this->year = (!empty($this->_data['Track']['Track']['year'])) ? (int)$this->_data['Track']['Track']['year']
                : 0;
        $this->genre1 = (!empty($this->_data['Track']['Track']['genre_1_id']))
                ? (int)$this->_data['Track']['Track']['genre_1_id'] : 0;
        $this->genre1 = (!empty($this->_data['Track']['Track']['genre_2_id']))
                ? (int)$this->_data['Track']['Track']['genre_2_id'] : 0;
        $this->genre1 = (!empty($this->_data['Track']['Track']['genre_3_id']))
                ? (int)$this->_data['Track']['Track']['genre_3_id'] : 0;
        $this->year = (!empty($this->_data['Track']['Track']['year'])) ? (int)$this->_data['Track']['Track']['year']
                : 0;
        $this->year = (!empty($this->_data['Track']['Track']['year'])) ? (int)$this->_data['Track']['Track']['year']
                : 0;
        $this->high = (!empty($this->_data['Track']['Track']['high'])) ? (int)$this->_data['Track']['Track']['high']
                : 0;
        $this->length = (!empty($this->_data['Track']['Track']['length']))
                ? (string)$this->_data['Track']['Track']['length'] : '';
        $this->file = (!empty($this->_data['Track']['Track']['file'])) ? (string)$this->_data['Track']['Track']['file']
                : '';
        $this->rating = (!empty($this->_data['Track']['Track']['rating']))
                ? (int)$this->_data['Track']['Track']['rating'] : 0;
        $this->reviewCount = (!empty($this->_data['Track']['Track']['review_count']))
                ? (int)$this->_data['Track']['Track']['review_count'] : 0;

        $this->uid = (!empty($this->_data['Track']['Track']['uid'])) ? (int)$this->_data['Track']['Track']['uid'] : '';

        // Create track artist (if supplied)
        if (!empty($this->_data['Track']['Track']['TrackArtist'])) {
            $this->trackArtist = new TrackArtist;
            $this->trackArtist->load($this->_data['Track']['Track']);
        }

        // Create tags (if supplied)
        if (!empty($this->_data['TrackTag']) && is_array($this->_data['TrackTag'])) {
            $this->tags = array();
            foreach ($this->_data['TrackTag'] as $tagData) {
                $tag = new TrackTag();
                $tag->load(array('TrackTag' => $tagData));
                $this->tags[] = $tag;
            }
        }
    }

    protected function _exportArray() {
        $this->_data = array(
            'Track' => array(
                'track_name' => $this->trackName,
                'bpm' => $this->bpm,
                'year' => $this->year,
                'genre_1_id' => $this->genre1,
                'genre_2_id' => $this->genre2,
                'genre_3_id' => $this->genre3,
                'high' => $this->high,
                'length' => $this->length,
                'file' => $this->file,
                'rating' => $this->rating,
                'review_count' => $this->reviewCount,
                'uid' => $this->uid
            )
        );

        if (!empty($this->id)) $this->_data['Track']['id'] = $this->id;

        if (!empty($this->trackArtist)) {
            $trackArtistData = $this->trackArtist->save();
            $this->_data['Track']['TrackArtist'] = $trackArtistData['TrackArtist'];
            if (!empty($this->trackArtist->id)) {
                $this->_data['Track']['artist_id'] = $this->trackArtist->id;
            }
        }

        if (!empty($this->tags) && is_array($this->tags)) {
            foreach ($this->tags as $tag) {
                #if(is_a($track, 'Com\Kaleidovision\MusicPortal\Playlist\TrackTag')) {
                $tagData = $tag->save();

                if (!empty($this->id)) $tagData['TrackTag']['track_id'] = $this->id;

                $this->_data['Track']['TrackTag'][] = $tagData['TrackTag'];
                #}
            }
        }

        parent::_exportArray();
    }

    public function importDjv($xml = null) {
        if (empty($xml)) return false;
        if (!$this->_xml->loadXML($xml)) return false;

        if ($nodes = $this->_xml->getElementsByTagName('MEDIA-CLIP')) {
            if ($attributes = $nodes->item(0)->attributes) {
                if ($node = $attributes->getNamedItem('UID'))
                    $this->uid = $node->nodeValue;
            }

            if ($playNodes = $nodes->item(0)->getElementsByTagName('PLAY')) {
                if ($playClipNodes = $playNodes->item(0)->getElementsByTagName('PLAY-CLIP')) {
                    if ($attributes = $playClipNodes->item(0)->attributes)
                        $this->file = html_entity_decode(urldecode($attributes->getNamedItem('FILE-NAME')->nodeValue));
                    if (($startNode = $attributes->getNamedItem('START'))
                        && ($finishNode = $attributes->getNamedItem('FINISH'))
                    ) {
                        $milliseconds = $finishNode->nodeValue - $startNode->nodeValue;

                        $hours = floor($milliseconds / (1000 * 60 * 60));
                        $minutes = floor(($milliseconds % (1000 * 60 * 60)) / (1000 * 60));
                        $seconds = floor((($milliseconds % (1000 * 60 * 60)) % (1000 * 60)) / 1000);

                        $this->length = str_pad($hours, 2, 0, STR_PAD_LEFT) . ':' . str_pad($minutes, 2, 0, STR_PAD_LEFT) . ':' . str_pad($seconds, 2, 0, STR_PAD_LEFT);
                    }
                }
            }

            if ($infoNodes = $nodes->item(0)->getElementsByTagName('INFO')) {
                if ($attributes = $infoNodes->item(0)->attributes) {
                    if ($node = $attributes->getNamedItem('ARTIST'))
                        $this->trackArtist = new TrackArtist(urldecode($node->nodeValue));
                    if ($node = $attributes->getNamedItem('BPM'))
                        $this->bpm = $node->nodeValue;
                    if ($node = $attributes->getNamedItem('NAME'))
                        $this->trackName = urldecode($node->nodeValue);
                    if ($node = $attributes->getNamedItem('HIGHESTCHARTPOS'))
                        $this->high = $node->nodeValue;
                    if ($node = $attributes->getNamedItem('YEAR'))
                        $this->year = $node->nodeValue;
                    if ($node = $attributes->getNamedItem('GENRE1'))
                        $this->genre1 = $node->nodeValue + 1;
                    if ($node = $attributes->getNamedItem('GENRE2'))
                        $this->genre2 = $node->nodeValue + 1;
                    if ($node = $attributes->getNamedItem('GENRE3'))
                        $this->genre3 = $node->nodeValue + 1;
                }
            }

            if ($tagsNode = $nodes->item(0)->getElementsByTagName('TAGS')->item(0)) {
                if ($tagNodes = $tagsNode->getElementsByTagName('TAG')) {
                    $this->tags = array();
                    foreach($tagNodes as $node) {
                        if ($tagTitleNode = $node->attributes->getNamedItem('TAG'))
                            $this->tags[] = new TrackTag($tagTitleNode->nodeValue);
                    }
                }
            }
        }
    }

    public function importDjvProfiles($xml = null) {
        if (empty($xml)) {
            echo "XML Empty";
            return false;
        }
        if (!$this->_xml->loadXML($xml)) {
            echo "XML Load Failed";
            return false;
        }
        if ($nodes = $this->_xml->getElementsByTagName('MEDIA-CLIP')) {
            if ($attributes = $nodes->item(0)->attributes) {
                if ($node = $attributes->getNamedItem('UID')) {
                    $this->uid = $node->nodeValue;
                }
            }

            if ($playNodes = $nodes->item(0)->getElementsByTagName('PLAY')) {
                if ($playNodes->length==0)
                      return false;

                //echo "<br/>profile: " . $this->uid;
                for ($playNodesIdx=0; $playNodesIdx<$playNodes->length; $playNodesIdx++) {
                    if ($playClipNodes = $playNodes->item($playNodesIdx)->getElementsByTagName('PLAY-MEDIA-CLIP')) {
                        if ($attributes = $playClipNodes->item(0)->attributes)
                            $this->uids[] = $attributes->getNamedItem('UID')->nodeValue;
                    }
                }
            }

            if ($infoNodes = $nodes->item(0)->getElementsByTagName('INFO')) {
                if ($attributes = $infoNodes->item(0)->attributes) {
                    // profile name
                    if ($node = $attributes->getNamedItem('NAME'))
                        $this->trackName = urldecode($node->nodeValue);
                    // start date
                    if ($node = $attributes->getNamedItem('INDATE'))
                        $this->startDate = $this->_formatDateString($node->nodeValue);
                    //end date
                    if ($node = $attributes->getNamedItem('OUTDATE'))
                        $this->endDate = $this->_formatDateString($node->nodeValue);
                    // start time
                    if ($node = $attributes->getNamedItem('STARTTIME'))
                        $this->startTime = $this->_formatTimeString($node->nodeValue);
                    // end time
                    if ($node = $attributes->getNamedItem('FINISHTIME'))
                        $this->finishTime = $this->_formatTimeString($node->nodeValue);
                    // day of the week
                    if ($node = $attributes->getNamedItem('DAYOFWEEK'))
                        $this->dow = $node->nodeValue;
                }
            }
            return true;
        }
    }
}

/**
 * Class to hold a track and it's position in the playlist
 * @package PlaylistTrack
 */
class PlaylistTrack extends Data {
    public $track;
    public $position = 0;

    protected function _importDom() {
        parent::_importDom();

        if ($nodes = $this->_xml->getElementsByTagName('song')) {
            if ($node = $nodes->item(0)->attributes->getNamedItem('numb'))
                $this->position = $node->nodeValue;
        }

        $this->track = new Track();
        $this->track->loadDom($this->_xml);
    }

    protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');
        #if(is_a($this->track, 'Track')) {
        $this->track->file = 'c:\\clips\\mp3\\' . $this->track->file;
        $this->_xml = $this->track->saveDom();

        if ($nodes = $this->_xml->getElementsByTagName('song')) {
            if ($node = $nodes->item(0)) {
                $node->appendChild($this->_xml->createAttribute('numb'))->appendChild($this->_xml->createTextNode($this->position));
            }
        }
        #}

        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

        // Fill playlist track details
        $this->id = (!empty($this->_data['Track']['id'])) ? (int)$this->_data['Track']['id'] : 0;
        $this->position = (!empty($this->_data['Track']['PlaylistTrack']['position']))
                ? (int)$this->_data['Track']['PlaylistTrack']['position'] : 0;

        // Create track (if supplied)
        if (!empty($this->_data['Track'])) {
            $this->track = new Track;
            $this->track->load(array('Track' => $this->_data['Track']));
        }
    }

    protected function _exportArray() {
        $this->_data = array(
            'PlaylistTrack' => array(
                'id' => $this->id,
                'position' => $this->position
            )
        );

        #if(is_a($this->track, 'Track')) {
        $track = $this->track->save();
        $this->_data['PlaylistTrack']['Track'] = $track['Track'];
        $this->_data['PlaylistTrack']['track_id'] = $this->track->id;
        #}

        parent::_exportArray();
    }
}

/**
 * Playlist class
 * @package Playlist
 */
class Playlist extends Data {
    public $id = 0;
	public $userId = 0;
    public $name = '';
    public $author = '';
    public $imageFile = ''; // TODO: Determine whether images will be supported
    public $dateAdded = '0000-00-00 00:00:00';
    public $dayPart = 0;
    public $weekPart = 0;
    public $timesPlayed = 0; // TODO: Determine what this is for...
    public $file = ''; // Not really required...
    public $rating = 0;
    public $reviewCount = 0;
    public $ratingCount = 0;
    public $length = '00:00:00';
	public $status = 'New';

    public $tracks;

    protected function _importDom() {
        parent::_importDom();

        $this->dateAdded = date('Y-m-d H:i:s');

        if ($nodes = $this->_xml->getElementsByTagName('list')) {
            if ($attributes = $nodes->item(0)->attributes) {
                if ($node = $attributes->getNamedItem('music-portal-id'))
                    $this->id = $node->nodeValue;
				if ($node = $attributes->getNamedItem('user-id'))
                    $this->userId = $node->nodeValue;
                if ($node = $attributes->getNamedItem('name'))
                    $this->name = $node->nodeValue;
                if ($node = $attributes->getNamedItem('author'))
                    $this->author = $node->nodeValue;
                if ($node = $attributes->getNamedItem('day-part'))
                    $this->dayPart = $node->nodeValue;
                if ($node = $attributes->getNamedItem('week-part'))
                    $this->weekPart = $node->nodeValue;
                if ($node = $attributes->getNamedItem('overrideduration'))
                    $this->length = date('H:i:s', $node->nodeValue / (1000 * 60 * 60));
            }

            if ($tracks = $nodes->item(0)->getElementsByTagName('song')) {
                $this->tracks = array();
                foreach ($tracks as $track) {
                    $playlistTrack = new PlaylistTrack();
                    $playlistTrack->loadDom($track);
                    $this->tracks[] = $playlistTrack;
                }
            }
        }

        return true;
    }

    protected function _exportDom() {
        $this->_xml = new \DOMDocument('1.0');

        $playlist = $this->_xml->appendChild($this->_xml->createElement('list'));

        $playlist->appendChild($this->_xml->createAttribute('music-portal-id'))->appendChild($this->_xml->createTextNode($this->id));
		$playlist->appendChild($this->_xml->createAttribute('user-id'))->appendChild($this->_xml->createTextNode($this->userId));
        $playlist->appendChild($this->_xml->createAttribute('name'))->appendChild($this->_xml->createTextNode($this->name));
        $playlist->appendChild($this->_xml->createAttribute('author'))->appendChild($this->_xml->createTextNode($this->name));
        $playlist->appendChild($this->_xml->createAttribute('day-part'))->appendChild($this->_xml->createTextNode($this->dayPart));
        $playlist->appendChild($this->_xml->createAttribute('week-part'))->appendChild($this->_xml->createTextNode($this->weekPart));

        $seconds = date('H', strtotime($this->length)) * 60 * 60;
        $seconds += date('i', strtotime($this->length)) * 60;
        $seconds += date('s', strtotime($this->length));
        $playlist->appendChild($this->_xml->createAttribute('overrideduration'))->appendChild($this->_xml->createTextNode($seconds));
		
		// Disable shuffle playlist on load - perhaps add this option later on?
		$playlist->appendChild($this->_xml->createAttribute('shuffleonload'))->appendChild($this->_xml->createTextNode(0));

        $count = 0;
        if (!empty($this->tracks) && is_array($this->tracks)) {
            foreach ($this->tracks as $track) {
                $playlist->appendChild($this->_xml->importNode($track->saveDom()->firstChild, true));
                $count++;
            }
        }

        $playlist->appendChild($this->_xml->createAttribute('count'))->appendChild($this->_xml->createTextNode($count));

        parent::_exportDom();
    }

    protected function _importArray() {
        parent::_importArray();

        // Fill playlist details
        $this->id = (!empty($this->_data['Playlist']['id'])) ? (int)$this->_data['Playlist']['id'] : '';
		$this->userId = (!empty($this->_data['Playlist']['user_id'])) ? (int)$this->_data['Playlist']['user_id'] : '';
        $this->name = (!empty($this->_data['Playlist']['title'])) ? (string)$this->_data['Playlist']['title'] : '';

        $this->author = (!empty($this->_data['Playlist']['author'])) ? (string)$this->_data['Playlist']['author'] : '';

        $this->dateAdded = (!empty($this->_data['Playlist']['date_added']))
                ? (string)$this->_data['Playlist']['date_added'] : '0000-00-00 00:00:00';

        $this->dayPart = (!empty($this->_data['Playlist']['day_part_id']))
                ? (int)$this->_data['Playlist']['day_part_id'] : 0;

        $this->weekPart = (!empty($this->_data['Playlist']['week_part_id']))
                ? (int)$this->_data['Playlist']['week_part_id'] : 0;

        $this->timesPlayed = (!empty($this->_data['Playlist']['times_played']))
                ? (int)$this->_data['Playlist']['times_played'] : 0;

        $this->length = (!empty($this->_data['Playlist']['length'])) ? (string)$this->_data['Playlist']['length']
                : '00:00:00';

        $this->file = (!empty($this->_data['Playlist']['file'])) ? (string)$this->_data['Playlist']['file'] : '';

        $this->rating = (!empty($this->_data['Playlist']['rating'])) ? (int)$this->_data['Playlist']['rating'] : 0;

        $this->reviewCount = (!empty($this->_data['Playlist']['review_count']))
                ? (int)$this->_data['Playlist']['review_count'] : 0;

        $this->ratingCount = (!empty($this->_data['Playlist']['rating_count']))
                ? (int)$this->_data['Playlist']['rating_count'] : 0;

        // Create tracks (if supplied)
        if (!empty($this->_data['Track']) && is_array($this->_data['Track'])) {
            $this->tracks = array();
            foreach ($this->_data['Track'] as $playlistTrackData) {
                $playlistTrack = new PlaylistTrack();
                $playlistTrack->load(array('Track' => $playlistTrackData));
                $this->tracks[] = $playlistTrack;
            }
        }
    }

    protected function _exportArray() {
        $this->_data = array(
            'Playlist' => array(
                'id' => $this->id,
				'user_id' => $this->userId,
                'title' => $this->name,
                'author' => $this->author,
                'date_added' => $this->dateAdded,
                'day_part_id' => $this->dayPart,
                'week_part_id' => $this->weekPart,
                'times_played' => $this->timesPlayed,
                'file' => $this->file,
                'rating' => $this->rating,
                'length' => $this->length,
                'review_count' => $this->reviewCount,
                'rating_count' => $this->ratingCount
            ),
            'PlaylistTrack' => array()
        );

        if (!empty($this->tracks) && is_array($this->tracks)) {
            foreach ($this->tracks as $track) {
                #if(is_a($track, 'Com\Kaleidovision\MusicPortal\Playlist\PlaylistTrack')) {
                $this->_data['PlaylistTrack'][] = $track->save();
                #}
            }
        }

        parent::_exportArray();
    }

    public function sendToVenue($entities = null) {
        $date = date('Y-m-d-Hi');

        $configuration = Configuration::singleton();
        $zipFileLocation = $configuration->tempDirectory . 'UPS/';

        $zipFile = 'cfg.UPS.' . $date . '.MUS_PTL.zip';

        // Create the UPS zip archive
        $zip = new \ZipArchive();

        $zip->open($zipFileLocation . $zipFile, \ZipArchive::CREATE);
        $zip->addFromString('empty', '');
        $zip->addFromString('playlists/mp_list_' . $this->id . '.kvl', $this->saveXml());
        $zip->close();

        foreach ($entities as $entityId) {
            $autoUpdate = new AutoUpdate();
            $serverLocation = 'kvAutoUpdate/publish/configzips/' . $entityId . '/UPS/cfg.UPS.' . $entityId . '.' . $date . '.MUS_PTL.zip';
            if ($autoUpdate->publishFile($zipFileLocation . $zipFile, $serverLocation, $entityId, $date)) {
                $autoUpdate->hawk(array('UPS'));
            }
        }

        // Remove the UPS zip archive
        unlink($zipFileLocation . $zipFile);

        return true;
    }

    public function uploadToMusicPortal() {
        $configuration = Configuration::singleton();
        $tmp = null;
        if (empty($this->file)) {
            $tmp = true;
            $this->file = $configuration->tempDirectory . '/tmp_playlist_' . time() . '.kvl';
            $this->saveXml($this->file);
        }

        $url = 'http://' . $configuration->liveMusicPortalIPAddress . '/playlists/upload.xml';
        $formVars = array();
        $formVars['data[file]'] = "@$this->file";
        $formVars['data[name]'] = $this->name;
        $formVars['data[author]'] = $this->author;
        $formVars['data[day_part]'] = $this->dayPart;
        $formVars['data[week_part]'] = $this->weekPart;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Authorization: TRUEREST userid=$configuration->mpUserId&password=$configuration->mpPassword&apikey=whatever&class=Customer&username=Whatever"
		)); // TODO: tweak the rest API authentication
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formVars);
        $result = curl_exec($ch);

        if ($tmp) unlink($this->file);

        if (simplexml_load_string($result) !== false) {
            $xml = new \SimpleXMLElement($result);
            if (!empty($xml->meta->status)) {
                if ($xml->meta->status == 'ok') {
                    return $xml;
                } else {
                    $this->error = 'Server error: ' . $xml->meta->feedback->item->message;
                }
            } else {
                $this->error = 'Bad server response.';
            }
        } else {
            $error = curl_error($ch);
            if (!empty($error)) {
                $this->error = 'CURL Error: ' . $error;
            } else {
                $this->error = 'Bad server response.';
            }
        }
        return false;
    }
}

class Playlists { // Note: does not extend Data class
    public $playlists;

    public function loadFromDirectory($directory = null) {
        if (!is_dir($directory)) return false;

        if ($handle = opendir($directory)) {
            $playlists = array();

            while (($file = readdir($handle)) !== false) {
                if ($file == '..' or $file == '.' or $file == 'history.kvl') continue;
                $info = pathinfo($file);
                if ($info['extension'] != 'kvl') continue;

                $playlist = new Playlist();
                $playlist->loadXmlFile($directory . $file);
                $playlist->file = $directory . $file;
                $this->playlists[] = $playlist;
            }
        } else {
            return false;
        }
    }
}
?>