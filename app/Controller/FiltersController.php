<?php
App::uses('AppController', 'Controller');

/**
 * Filters Controller
 *
 * @property Tag $Tag
 */
class FiltersController extends AppController {
    public $components = array('RequestHandler');
    public $uses = array('Tag');
    public function index() {
    

        $alphabet = range('A', 'Z'); // Array of alphabets from A to Z
        $data = array();
    
        foreach ($alphabet as $letter) {
            $data[] = array(
            'title' => $letter
            );
        }
    
        $this->set('data', $data);
        $this->set('_serialize', array('data'));
      }
    /**
     * index method
     *
     * @return void
     */
	public function index() {
        if(empty($this->appConfig['app']['filters']))
            throw new Exception('No filters defined');

        $data = array();
        foreach($this->appConfig['app']['filters'] as $filterTitle => $filter) {
            $conditions = array();
            foreach($filter as $tagText) {
                #$conditions[] = 'Tag.title LIKE "%' . $tagText . '%"';
                $conditions[] = 'Tag.title LIKE "' . $tagText . '"';
            }

            $tags = $this->Tag->find('all', array(
                'conditions' => array(
                    'or' => $conditions
                ),
                'order' => array(
                    'Tag.title' => 'asc',
                    'Tag.id' => 'asc'
                ),
                'contain' => false
            ));

            if(!empty($tags)) {
                $data[] = array(
                    'title' => $filterTitle,
                    'Tag' => Set::classicExtract($tags, '{n}.Tag')
                );
            }
        }

        $this->set('data', $data);
        $this->set('_serialize', array('data'));

		//$this->set('data', Set::classicExtract($tweets, '{n}.Tweet'));*/
	}
}