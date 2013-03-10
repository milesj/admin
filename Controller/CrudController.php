<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class CrudController extends AdminAppController {

	/**
	 * Paginate defaults.
	 *
	 * @var array
	 */
	public $paginate = array();

	/**
	 * List out and paginate all the records in the model.
	 */
	public function index() {
		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array($this->Model->alias . '.' . $this->Model->displayField => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		), $this->Model->admin['paginate']);

		$this->setBelongsToData();

		// Filters
		if (!empty($this->request->params['named'])) {
			$this->paginate['conditions'] = $this->parseFilterConditions($this->request->params['named']);
		}

		// Batch delete
		if ($this->request->is('post')) {
			if (!$this->Model->admin['batchDelete']) {
				throw new ForbiddenException();

			} else if (!$this->Acl->check(array(USER_MODEL => $this->Auth->user()), $this->Model->qualifiedName, 'delete')) {
				throw new UnauthorizedException(__('Insufficient Access Permissions'));
			}

			$count = 0;
			$deleted = array();

			foreach ($this->request->data[$this->Model->alias] as $id) {
				if ($id && $this->Model->delete($id, true)) {
					$count++;
					$deleted[] = $id;
				}
			}

			if ($count > 0) {
				$this->logEvent(ActionLog::BATCH_DELETE, $this->Model, sprintf('Deleted IDs: %s', implode(', ', $deleted)));
				$this->Session->setFlash(__('%s %s have been deleted', array($count, strtolower($this->Model->pluralName))), 'flash', array('class' => 'success'));
			}
		}

		$this->set('results', $this->paginate($this->Model));
		$this->render('Crud/index');
	}

	/**
	 * Create a new record.
	 */
	public function create() {
		$this->setBelongsToData();
		$this->setHabtmData();

		if ($this->request->is('post')) {
			$data = $this->getRequestData();
			$this->Model->create($data);

			if ($this->Model->saveAll(null, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Model->set($data);
				$this->logEvent(ActionLog::CREATE, $this->Model);

				$this->setFlashMessage('Successfully created a new %s');
				$this->redirectAfter();

			} else {
				$this->setFlashMessage('Failed to create a new %s', null, 'error');
			}
		}

		$this->render('Crud/form');
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

		$this->Model->set($result);
		$this->logEvent(ActionLog::READ, $this->Model);

		$this->set('result', $result);
		$this->render('Crud/read');
	}

	/**
	 * Update a record.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 * @throws ForbiddenException
	 */
	public function update($id) {
		if (!$this->Model->admin['editable']) {
			throw new ForbiddenException();
		}

		$this->Model->id = $id;

		$result = $this->Model->find('first', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->primaryKey => $id),
			'contain' => $this->getDeepRelations(false)
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->setBelongsToData();
		$this->setHabtmData();

		if ($this->request->is('put')) {
			$data = $this->getRequestData();

			if ($this->Model->saveAll($data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Model->set($result);
				$this->logEvent(ActionLog::UPDATE, $this->Model);

				$this->setFlashMessage('Successfully updated %s with ID %s', $id);
				$this->redirectAfter();

			} else {
				$this->setFlashMessage('Failed to update %s with ID %s', $id, 'error');
			}
		} else {
			$this->request->data = $result;
		}

		$this->set('result', $result);
		$this->render('Crud/form');
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
				$this->logEvent(ActionLog::DELETE, $this->Model);
				$this->setFlashMessage('Successfully deleted %s with ID %s', $id);
				$this->redirectAfter();

			} else {
				$this->setFlashMessage('Failed to delete %s with ID %s', $id, 'error');
			}
		}

		$this->set('result', $result);
		$this->render('Crud/delete');
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
	 * Proxy action to handle POST requests and redirect back with named params.
	 */
	public function proxy() {
		if (empty($this->request->data[$this->Model->alias])) {
			$this->redirect($this->referer());
		}

		$data = $this->request->data[$this->Model->alias];
		$named = array();

		foreach ($data as $key => $value) {
			if (
				substr($key, -7) === '_filter' ||
				substr($key, -11) === '_type_ahead' || 
				$value === '') {
				continue;
			}

			$named[$key] = urlencode($value);

			if (isset($data[$key . '_filter'])) {
				$named[$key . '_filter'] = urlencode($data[$key . '_filter']);
			}
		}

		$url = array(
			'controller' => 'crud',
			'action' => 'index',
			'model' => $this->Model->urlSlug
		);

		$this->redirect(array_merge($named, $url));
	}

	/**
	 * Validate the user has the correct CRUD access permission.
	 *
	 * @param array $user
	 * @return bool
	 * @throws ForbiddenException
	 * @throws UnauthorizedException
	 */
	public function isAuthorized($user = null) {
		parent::isAuthorized($user);

		if (empty($this->params['model'])) {
			throw new ForbiddenException(__('Invalid Model'));
		}

		list($plugin, $model, $class) = Admin::parseName($this->params['model']);

		// Don't allow certain models
		if (in_array($class, Configure::read('Admin.ignoreModels'))) {
			throw new ForbiddenException(__('Restricted Model'));
		}

		$action = $this->action;

		// Allow type ahead and proxy
		if ($action === 'type_ahead' || $action === 'proxy') {
			return true;

		// Index counts as a read
		} else if ($action === 'index') {
			$action = 'read';
		}

		if ($this->Acl->check(array('User' => $user), $class, $action)) {
			return true;
		}

		throw new UnauthorizedException(__('Insufficient Access Permissions'));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Introspect model
		if (isset($this->params['model'])) {
			list($plugin, $model, $class) = Admin::parseName($this->params['model']);

			// Don't allow certain models
			if (in_array($class, Configure::read('Admin.ignoreModels'))) {
				throw new ForbiddenException(__('Restricted Model'));
			}

			$this->Model = Admin::introspectModel($class);
		}

		// Parse request and set null fields to null
		if ($data = $this->request->data) {
			foreach ($data as $model => $fields) {
				foreach ($fields as $key => $value) {
					if (substr($key, -5) === '_null' && $value) {
						$data[$model][str_replace('_null', '', $key)] = null;
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
	 * Return the request data after processing the fields.
	 *
	 * @return array
	 */
	protected function getRequestData() {
		$data = $this->request->data;

		if ($data) {
			foreach ($data as $model => $fields) {
				foreach ($fields as $key => $value) {
					if (
						(substr($key, -5) === '_null') ||
						(substr($key, -11) === '_type_ahead') ||
						in_array($key, array('redirect_to'))
					) {
						unset($data[$model][$key]);
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Parse the request into an array of filtering SQL conditions.
	 *
	 * @param array $data
	 * @return array
	 */
	protected function parseFilterConditions($data) {
		$conditions = array();
		$fields = $this->Model->fields;
		$alias = $this->Model->alias;
		$enum = $this->Model->enum;

		foreach ($data as $key => $value) {
			if (substr($key, -7) === '_filter') {
				continue;
			}

			$field = $fields[$key];
			$value = urldecode($value);

			// Dates, times, numbers
			if (isset($data[$key . '_filter'])) {
				$operator = urldecode($data[$key . '_filter']);
				$operator = ($operator === '=') ? '' : ' ' . $operator;

				if ($field['type'] === 'datetime') {
					$value = date('Y-m-d H:i:s', strtotime($value));

				} else if ($field['type'] === 'date') {
					$value = date('Y-m-d', strtotime($value));

				} else if ($field['type'] === 'time') {
					$value = date('H:i:s', strtotime($value));
				}

				$conditions[$alias . '.' . $key . $operator] = $value;

			// Enums, booleans, relations
			} else if (isset($enum[$key]) || $field['type'] === 'boolean' || !empty($field['belongsTo'])) {
				$conditions[$alias . '.' . $key] = $value;

			// Strings
			} else {
				$conditions[$alias . '.' . $key . ' LIKE'] = '%' . $value . '%';
			}
		}

		// Set data to use in form
		$this->request->data[$this->Model->alias] = $data;

		return $conditions;
	}

	/**
	 * Redirect after a create or update.
	 *
	 * @param string $action
	 */
	protected function redirectAfter($action = null) {
		if (!$action) {
			$action = $this->request->data[$this->Model->alias]['redirect_to'];
		}

		$url = array('controller' => strtolower($this->name), 'action' => $action, 'model' => $this->Model->urlSlug);

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
	protected function setBelongsToData() {
		$typeAhead = array();

		foreach ($this->Model->belongsTo as $alias => $assoc) {
			$model = Admin::introspectModel($assoc['className']);
			$count = $model->find('count');

			// Add to type ahead if too many records
			if ($count > $this->Model->admin['associationLimit']) {
				$class = $assoc['className'];

				if (strpos($class, '.') === false) {
					$class = Configure::read('Admin.coreName') . '.' . $class;
				}

				$typeAhead[$assoc['foreignKey']] = array(
					'alias' => $alias,
					'model' => $class
				);

			} else {
				$variable = Inflector::variable(Inflector::pluralize(preg_replace('/(?:_id)$/', '', $assoc['foreignKey'])));

				// Use Tree if available
				if ($model->hasMethod('generateTreeList')) {
					$list = $model->generateTreeList(null, null, null, ' -- ');

				} else {
					$list = $model->find('list', array(
						'order' => array($model->alias . '.' . $model->displayField => 'ASC')
					));
				}

				$this->set($variable, $list);
			}
		}

		$this->set('typeAhead', $typeAhead);
	}

	/**
	 * Set hasAndBelongsToMany data for forms. This allows for saving of associated data.
	 */
	protected function setHabtmData() {
		foreach ($this->Model->hasAndBelongsToMany as $assoc) {
			if (!$assoc['showInForm']) {
				continue;
			}

			$model = Admin::introspectModel($assoc['className']);
			$variable = Inflector::variable(Inflector::pluralize(preg_replace('/(?:_id)$/', '', $assoc['associationForeignKey'])));

			$this->set($variable, $model->find('list', array(
				'order' => array($model->alias . '.' . $model->displayField => 'ASC')
			)));
		}
	}

	/**
	 * Convenience method to set a flash message.
	 *
	 * @param string $message
	 * @param int $id
	 * @param string $type
	 */
	protected function setFlashMessage($message, $id = null, $type = 'success') {
		if (!$id) {
			$id = $this->Model->id;
		}

		$this->Session->setFlash(__($message, array(strtolower($this->Model->singularName), $id)), 'flash', array('class' => $type));
	}

}