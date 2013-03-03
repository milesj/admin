<?php

/**
 * @property Model $Model
 */
class CrudController extends AdminAppController {

	public function index() {
		$this->paginate = array(
			'limit' => $this->Model->admin['paginateLimit'],
			'order' => array($this->Model->alias . '.' . $this->Model->primaryKey => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		);

		// Batch delete
		if ($this->request->is('post') && $this->Model->admin['batchDelete']) {
			debug($this->request->data);
		}

		$this->set('results', $this->paginate($this->Model));
	}

	public function create() {
		$this->render('form');
	}

	public function read($id) {
		$contain = array_keys($this->Model->belongsTo) + array_keys($this->Model->hasOne);
		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => $contain,
			'cache' => false,
			'recursive' => -1
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->set('result', $result);
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