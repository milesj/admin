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

	/**
	 * Create a new record.
	 */
	public function create() {
		$this->Model->create();
		$this->setAssociatedData();

		if ($this->request->is('post')) {
			if ($this->Model->saveAssociated($this->request->data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Session->setFlash(__('Successfully created a new %s', $this->Model->alias), 'flash', array('class' => 'success'));
				$this->redirectAfter($this->request->data[$this->Model->alias]['redirect_to']);

			} else {
				$this->Session->setFlash(__('Failed to create a new %s', $this->Model->alias), 'flash', array('class' => 'error'));
			}
		}

		$this->render('form');
	}

	/**
	 * Read a record and associated records.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function read($id) {
		$this->Model->id = $id;

		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => $this->getDeepRelations()
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->set('result', $result);
	}

	/**
	 * Update a record.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function update($id) {
		$this->Model->id = $id;

		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => $this->getDeepRelations(false)
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->setAssociatedData();

		if ($this->request->is('put')) {
			if ($this->Model->saveAssociated($this->request->data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Session->setFlash(__('Successfully updated %s with ID %s', array($this->Model->alias, $id)), 'flash', array('class' => 'success'));
				$this->redirectAfter($this->request->data[$this->Model->alias]['redirect_to']);

			} else {
				$this->Session->setFlash(__('Failed to update %s with ID %s', array($this->Model->alias, $id)), 'flash', array('class' => 'error'));
			}
		} else {
			$this->request->data = $result;
		}

		$this->set('result', $result);
		$this->render('form');
	}

	/**
	 * Delete a record and all associated records after delete confirmation.
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
				$this->Session->setFlash(__('Successfully deleted %s with ID %s', array($this->Model->alias, $id)), 'flash', array('class' => 'success'));
				$this->redirect(array('action' => 'index', 'model' => $this->params['model']));

			} else {
				$this->Session->setFlash(__('Failed to delete %s with ID %s', array($this->Model->alias, $id)), 'flash', array('class' => 'error'));
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

		if (empty($this->request->query['query'])) {
			throw new BadRequestException();
		}

		$results = $this->Model->find('list', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->displayField . ' LIKE' => '%' . $this->request->query['query'] . '%'),
			'contain' => false
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

		// Introspect model
		if (isset($this->params['model'])) {
			list($plugin, $model) = pluginSplit($this->params['model']);
			$plugin = Inflector::camelize($plugin);
			$model = Inflector::camelize($model);

			$this->Model = Introspect::load($plugin . '.' . $model);
		}

		// Parse request data
		if ($data = $this->request->data) {
			foreach ($data as $model => $fields) {
				foreach ($fields as $key => $value) {
					// Remove null fields and set parent to null
					if (substr($key, -5) === '_null') {
						if ($value) {
							$data[$model][str_replace('_null', '', $key)] = null;
						}

						unset($data[$model][$key]);
					}

					// Remove type ahead fields
					if (substr($key, -11) === '_type_ahead') {
						unset($data[$model][$key]);
					}
				}
			}

			$this->request->data = $data;
		}
	}

	/**
	 * Get a list of valid containable model relations.
	 * Should also get belongsTo data for hasOne and hasMany.
	 *
	 * @param bool $extended
	 * @return array
	 */
	protected function getDeepRelations($extended = true) {
		$contain = array_keys($this->Model->belongsTo);
		$contain = array_merge($contain, array_keys($this->Model->hasAndBelongsToMany));

		if ($extended) {
			foreach (array($this->Model->hasOne, $this->Model->hasMany) as $assocs) {
				foreach ($assocs as $alias => $assoc) {
					$contain[$alias] = array_keys($this->Model->{$alias}->belongsTo);
				}
			}
		}

		return $contain;
	}

	/**
	 * Redirect after a create or update.
	 *
	 * @param string $action
	 */
	protected function redirectAfter($action) {
		$url = array('controller' => 'crud', 'action' => $action, 'model' => $this->Model->urlSlug);

		switch ($action) {
			case 'read':
			case 'update':
			case 'delete':
				$url[] = $this->Model->id;
			break;
		}

		$this->redirect($url);
	}

	/**
	 * Set belongsTo data for select inputs. If there are too many records, switch to type ahead.
	 */
	protected function setAssociatedData() {
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