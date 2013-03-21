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
				throw new ForbiddenException(__d('admin', 'Delete Access Protected'));

			} else if (!$this->Acl->check(array(USER_MODEL => $this->Auth->user()), $this->Model->qualifiedName, 'delete')) {
				throw new UnauthorizedException(__d('admin', 'Insufficient Access Permissions'));
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
				$this->AdminToolbar->setFlashMessage(__d('admin', '%s %s have been deleted', array($count, mb_strtolower($this->Model->pluralName))));
			}
		}

		$this->set('results', $this->paginate($this->Model));
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

				$this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully created a new %s', mb_strtolower($this->Model->singularName)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to create a new %s', mb_strtolower($this->Model->singularName)), 'error');
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

		$result = $this->AdminToolbar->getRecordById($this->Model, $id);

		if (!$result) {
			throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
		}

		$this->Model->set($result);
		$this->AdminToolbar->logAction(ActionLog::READ, $this->Model, $id);

		$this->set('result', $result);
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
			throw new ForbiddenException(__d('admin', 'Update Access Protected'));
		}

		$this->Model->id = $id;

		$result = $this->AdminToolbar->getRecordById($this->Model, $id, false);

		if (!$result) {
			throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
		}

		$this->AdminToolbar->setBelongsToData($this->Model);
		$this->AdminToolbar->setHabtmData($this->Model);

		if ($this->request->is('put')) {
			$data = $this->AdminToolbar->getRequestData();

			if ($this->Model->saveAll($data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
				$this->Model->set($result);
				$this->AdminToolbar->logAction(ActionLog::UPDATE, $this->Model, $id);

				$this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully updated %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to update %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)), 'error');
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
			throw new ForbiddenException(__d('admin', 'Delete Access Protected'));
		}

		$this->Model->id = $id;

		$result = $this->AdminToolbar->getRecordById($this->Model, $id);

		if (!$result) {
			throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
		}

		if ($this->request->is('post')) {
			if ($this->Model->delete($id, true)) {
				$this->AdminToolbar->logAction(ActionLog::DELETE, $this->Model, $id);

				$this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully deleted %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to delete %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)), 'error');
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
			throw new BadRequestException(__d('admin', 'Missing Query'));
		}

		$results = $this->Model->find('list', array(
			'conditions' => array($this->Model->alias . '.' . $this->Model->displayField . ' LIKE' => '%' . $this->request->query['query'] . '%'),
			'contain' => false
		));

		$this->set('results', $results);
		$this->set('_serialize', 'results');
	}

	/**
	 * Trigger a callback method on the model.
	 *
	 * @param int $id
	 * @param string $method
	 * @throws NotFoundException
	 */
	public function process_model($id, $method) {
		$model = $this->Model;
		$record = $this->AdminToolbar->getRecordById($model, $id);

		if (!$record) {
			throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
		}

		if ($model->hasMethod($method)) {
			$model->{$method}($id);

			$this->AdminToolbar->logAction(ActionLog::PROCESS, $model, $id, __d('admin', 'Triggered %s.%s() process', array($model->alias, $method)));
			$this->AdminToolbar->setFlashMessage(__d('admin', 'Processed %s.%s() for ID %s', array($model->alias, $method, $id)));

		} else {
			$this->AdminToolbar->setFlashMessage(__d('admin', '%s does not allow this process', $model->singularName), 'error');
		}

		$this->redirect($this->referer());
	}

	/**
	 * Trigger a callback method on the behavior.
	 *
	 * @param string $behavior
	 * @param string $method
	 */
	public function process_behavior($behavior, $method) {
		$behavior = Inflector::camelize($behavior);
		$model = $this->Model;

		if ($model->Behaviors->loaded($behavior) && $model->hasMethod($method)) {
			$model->Behaviors->{$behavior}->{$method}($model);

			$this->AdminToolbar->logAction(ActionLog::PROCESS, $model, null, __d('admin', 'Triggered %s.%s() process', array($behavior, $method)));
			$this->AdminToolbar->setFlashMessage(__d('admin', 'Processed %s.%s() for %s', array($behavior, $method, mb_strtolower($model->pluralName))));

		} else {
			$this->AdminToolbar->setFlashMessage(__d('admin', '%s does not allow this process', $model->singularName), 'error');
		}

		$this->redirect($this->referer());
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
			throw new ForbiddenException(__d('admin', 'Invalid Model'));
		}

		list($plugin, $model, $class) = Admin::parseName($this->params['model']);

		// Don't allow certain models
		if (in_array($class, Configure::read('Admin.ignoreModels'))) {
			throw new ForbiddenException(__d('admin', 'Restricted Model'));
		}

		$action = $this->action;

		// Allow non-crud actions
		if (in_array($action, array('type_ahead', 'proxy', 'process_behavior', 'process_model'))) {
			return true;

		// Index counts as a read
		} else if ($action === 'index') {
			$action = 'read';
		}

		if ($this->Acl->check(array(USER_MODEL => $user), $class, $action)) {
			return true;
		}

		throw new UnauthorizedException(__d('admin', 'Insufficient Access Permissions'));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Introspect model
		if (isset($this->params['model'])) {
			$this->Model = Admin::introspectModel($this->params['model']);
		}

		// Parse request and set null fields to null
		if ($data = $this->request->data) {
			foreach ($data as $model => $fields) {
				foreach ($fields as $key => $value) {
					if (mb_substr($key, -5) === '_null' && $value) {
						$data[$model][str_replace('_null', '', $key)] = null;
					}
				}
			}

			$this->request->data = $data;
		}
	}

}