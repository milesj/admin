<?php

/**
 * @property Model $Model
 */
class CrudController extends AdminAppController {

	public function index() {
		$this->paginate = array(
			'limit' => 25,
			'order' => array($this->Model->alias . '.id' => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		);

		$this->set('results', $this->paginate($this->Model));
	}

	public function create() {
		$this->render('form');
	}

	public function read($id) {

	}

	public function update($id) {
		$this->render('form');
	}

	public function delete($id) {

	}

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow();
	}

}