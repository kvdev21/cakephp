<?php
App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * Genres Controller
 *
 * @property Genre $Genre
 */
class GenresController extends AppController {
    public $components = array('RequestHandler');

    /**
     * index method
     *
     * @return void
     */
	public function index() {
        $genres = $this->Genre->find('all', array(
            'conditions' => array(
                'Genre.id' => $this->appConfig['genres']
            ),
            'order' => array(
                'Genre.title' => 'ASC',
                'Genre.id' => 'ASC'
            ),
            'contain' => false
        ));

        $data = Set::classicExtract($genres, '{n}.Genre');

        if(!empty($data)) {
            foreach($data as &$genre) {
                $genre['title'] = strtolower($genre['title']);
            }
            unset($genre);
        }

        $this->set('data', $data);
        $this->set('_serialize', array('data'));

		//$this->set('data', Set::classicExtract($tweets, '{n}.Tweet'));*/
	}
}