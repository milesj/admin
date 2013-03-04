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

		$this->setAssociations();

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

		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => array_keys($this->Model->belongsTo)
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->setAssociations();

		if ($this->request->is('put')) {
			debug($this->request->data);
		} else {
			$this->request->data = $result;
		}

		$this->set('result', $result);
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

		$this->Model->id = $id;

		$result = $this->Model->read();

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
	 * Query the model for a list of records that match the term.
	 *
	 * @throws BadRequestException
	 */
	public function type_ahead() {
		$this->viewClass = 'Json';
		$this->layout = 'ajax';

		if (!$this->request->query) {
			throw new BadRequestException();
		}

		$model = ClassRegistry::init($this->request->query['model']);
		$results = $model->find('list', array(
			'conditions' => array($model->alias . '.' . $model->displayField . ' LIKE' => '%' . $this->request->query['query'] . '%'),
			'contain' => false,
		));

		$this->set('results', $results);
		$this->set('_serialize', 'results');
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow();
	}

	/**
	 * Set belongsTo data for select inputs. If there are too many records, switch to type ahead.
	 */
	protected function setAssociations() {
		$typeAhead = array();

		foreach ($this->Model->belongsTo as $alias => $assoc) {
			$belongsTo = $this->Model->{$alias};
			$count = $belongsTo->find('count');

			// Add to type ahead if too many records
			if ($count > $this->Model->admin['associationLimit']) {
				$typeAhead[$assoc['foreignKey']] = array(
					'alias' => $alias,
					'model' => $assoc['className']
				);

			} else {
				$variable = Inflector::variable(Inflector::pluralize(preg_replace('/(?:_id)$/', '', $assoc['foreignKey'])));
				$list = array();

				// Display ID and field in the list
				if ($results = $belongsTo->find('all')) {
					foreach ($results as $result) {
						$id = $result[$alias][$belongsTo->primaryKey];
						$display = $result[$alias][$belongsTo->displayField];

						if ($display != $id) {
							$display = $id . ' - ' . $display;
						}

						$list[$id] = $display;
					}
				}

				$this->set($variable, $list);
			}
		}

		$this->set('typeAhead', $typeAhead);
	}

}