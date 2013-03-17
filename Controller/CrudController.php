<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class CrudController extends AdminAppController {

	/**
	 * List out and paginate all the records in the model.
	 */
	public function index() {
		if ($this->params['model'] === 'admin.item_report') {
			$this->redirect(array('controller' => 'admin', 'action' => 'reports'));
		}

		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array($this->Model->alias . '.' . $this->Model->displayField => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		), $this->Model->admin['paginate']);

		$this->AdminToolbar->setBelongsToData($this->Model);

		// Filters
		if (!empty($this->request->params['named'])) {
			$this->paginate['conditions'] = $this->AdminToolbar->parseFilterConditions($this->Model, $this->request->params['named']);
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
				$this->AdminToolbar->logAction(ActionLog::BATCH_DELETE, $this->Model, null, sprintf('Deleted IDs: %s', implode(', ', $deleted)));
				$this->AdminToolbar->setFlashMessage(__('%s %s have been deleted', array($count, strtolower($this->Model->pluralName))));
			}
		}

		$this->set('results', $this->paginate($this->Model));
		$this->render('Crud/index');
	}

	/**
	 * Create a new record.
	 */
	public function create() {
		$this->AdminToolbar->setBelongsToData($this->Model);
		$this->AdminToolbar->setHabtmData($this->Model);

		if ($this->request->is('post')) {
			$data = $this->AdminToolbar->getRequestData();
			$this->Model->create($data);

			if ($this->Model->saveAll(null, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Model->set($data);
				$this->AdminToolbar->logAction(ActionLog::CREATE, $this->Model, $this->Model->id);

				$this->AdminToolbar->setFlashMessage(__('Successfully created a new %s', strtolower($this->Model->singularName)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__('Failed to create a new %s', strtolower($this->Model->singularName)), 'error');
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
			'contain' => $this->AdminToolbar->getDeepRelations($this->Model)
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->Model->set($result);
		$this->AdminToolbar->logAction(ActionLog::READ, $this->Model, $id);

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
			'contain' => $this->AdminToolbar->getDeepRelations($this->Model, false)
		));

		if (!$result) {
			throw new NotFoundException();
		}

		$this->AdminToolbar->setBelongsToData($this->Model);
		$this->AdminToolbar->setHabtmData($this->Model);

		if ($this->request->is('put')) {
			$data = $this->AdminToolbar->getRequestData();

			if ($this->Model->saveAll($data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Model->set($result);
				$this->AdminToolbar->logAction(ActionLog::UPDATE, $this->Model, $id);

				$this->AdminToolbar->setFlashMessage(__('Successfully updated %s with ID %s', array(strtolower($this->Model->singularName), $id)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__('Failed to update %s with ID %s', array(strtolower($this->Model->singularName), $id)), 'error');
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
				$this->AdminToolbar->logAction(ActionLog::DELETE, $this->Model, $id);

				$this->AdminToolbar->setFlashMessage(__('Successfully deleted %s with ID %s', array(strtolower($this->Model->singularName), $id)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__('Failed to delete %s with ID %s', array(strtolower($this->Model->singularName), $id)), 'error');
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
	 * Recover and reorder the models tree.
	 *
	 * @param string $behavior
	 * @param string $method
	 */
	public function process($behavior, $method) {
		$behavior = Inflector::camelize($behavior);
		$model = $this->Model;

		if ($model->Behaviors->loaded($behavior) && $model->hasMethod($method)) {
			$model->Behaviors->{$behavior}->{$method}($model);

			$this->AdminToolbar->logAction(ActionLog::PROCESS, $model, null, __('Triggered %s.%s() process', array($behavior, $method)));
			$this->AdminToolbar->setFlashMessage(__('Processed %s.%s() for %s', array($behavior, $method, strtolower($model->pluralName))));

		} else {
			$this->AdminToolbar->setFlashMessage(__('%s do not allow for this process', $model->pluralName), 'error');
		}

		$this->redirect($this->referer());
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

		// Allow non-crud actions
		if (in_array($action, array('type_ahead', 'proxy', 'process'))) {
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

}