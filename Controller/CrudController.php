<?php

/**
 * @property Model $Model
 */
class CrudController extends AdminAppController {

	/**
	 * List out and paginate all the records in the model.
	 */
	public function index() {
		$this->paginate = array(
			'limit' => $this->Model->admin['paginateLimit'],
			'order' => array($this->Model->alias . '.' . $this->Model->primaryKey => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		);

		// Batch delete
		if ($this->request->is('post')) {
			if (!$this->Model->admin['deletable']) {
				throw new ForbiddenException();
			}

			debug($this->request->data);
		}

		$this->set('results', $this->paginate($this->Model));
	}

	public function create() {
		$this->Model->create();

		$this->render('form');
	}

	public function read($id) {
		$contain = array_keys($this->Model->belongsTo);
		$contain = array_merge($contain, array_keys($this->Model->hasOne));
		$contain = array_merge($contain, array_keys($this->Model->hasAndBelongsToMany));

		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => $contain
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->set('result', $result);
	}

	public function update($id) {
		$this->Model->id = $id;

		$this->render('form');
	}

	/**
	 * Delete a record and all associated dependencies after delete confirmation.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 * @throws ForbiddenException
	 */
	public function delete($id) {
		if (!$this->Model->admin['deletable']) {
			throw new ForbiddenException();
		}

		$result = $this->Model->read(null, $id);

		if (!$result) {
			throw new NotFoundException();
		}

		if ($this->request->is('post')) {
			if ($this->Model->delete($id, true)) {
				$this->Session->setFlash(__('The %s with ID %s has been deleted', array($this->Model->alias, $id)), 'flash', array('class' => 'success'));
				$this->redirect(array('action' => 'index', 'model' => $this->params['model']));

			} else {
				$this->Session->setFlash(__('The %s with ID %s failed to delete', array($this->Model->alias, $id)), 'flash', array('class' => 'error'));
			}
		}

		$this->set('result', $result);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow();
	}

}