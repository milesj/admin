<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

class CrudController extends AdminAppController {

    /**
     * List out and paginate all the records in the model.
     */
    public function index() {
        if ($this->overrideAction('index')) {
            return;
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
            if (!$this->Model->admin['batchProcess']) {
                throw new ForbiddenException(__d('admin', 'Batch Access Protected'));
            }

            $action = $this->request->data[$this->Model->alias]['batch_action'];
            $items = $this->request->data[$this->Model->alias]['items'];
            $processed = array();

            foreach ($items as $id) {
                if (!$id) {
                    continue;
                }

                if ($action === 'delete_item') {
                    if ($this->Model->delete($id, true)) {
                        $processed[] = $id;
                    }
                } else {
                    if (call_user_func(array($this->Model, $action), $id)) {
                        $processed[] = $id;
                    }
                }
            }

            $count = count($processed);

            if ($count > 0) {
                $this->AdminToolbar->logAction(ActionLog::BATCH_DELETE, $this->Model, '', sprintf('Processed IDs: %s', implode(', ', $processed)));
                $this->AdminToolbar->setFlashMessage(__d('admin', '%s %s have been processed', array($count, mb_strtolower($this->Model->pluralName))));

                $this->request->data[$this->Model->alias] = array();
            }
        }

        $this->set('results', $this->paginate($this->Model));
        $this->overrideView('index');
    }

    /**
     * Export all the records in the model as CSV
     */
    public function export() {
        if ($this->overrideAction('export')) {
            return;
        }

        $this->paginate = array_merge(array(
            'limit' => 99999,
            'order' => array($this->Model->alias . '.' . $this->Model->displayField => 'ASC'),
            'contain' => array_keys($this->Model->belongsTo)
        ), $this->Model->admin['export']);

        // Filters
        if (!empty($this->request->params['named'])) {
            $conditions = $this->AdminToolbar->parseFilterConditions($this->Model, $this->request->params['named']);
        }

        // Output
        $results = $this->paginate($this->Model);
        $this->CsvView = $this->Components->load('CsvView.CsvView');
        $this->CsvView->startup($this);
        $this->CsvView->quickExport($results);
    }

    /**
     * Create a new record.
     */
    public function create() {
        if ($this->overrideAction('create')) {
            return;
        }

        $this->AdminToolbar->setBelongsToData($this->Model);
        $this->AdminToolbar->setHabtmData($this->Model);

        if ($this->request->is('post')) {
            $data = $this->AdminToolbar->getRequestData();
            $this->Model->create();

            if ($this->Model->saveAll($data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
                $this->Model->set($data);
                $this->AdminToolbar->logAction(ActionLog::CREATE, $this->Model, $this->Model->id);

                $this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully created a new %s', mb_strtolower($this->Model->singularName)));
                $this->AdminToolbar->redirectAfter($this->Model);

            } else {
                $this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to create a new %s', mb_strtolower($this->Model->singularName)), 'is-error');
            }
        }

        $this->overrideView('create');
    }

    /**
     * Read a record and associated records.
     *
     * @param int $id
     * @throws NotFoundException
     */
    public function read($id) {
        if ($this->overrideAction('read', $id)) {
            return;
        }

        $this->Model->id = $id;

        $result = $this->AdminToolbar->getRecordById($this->Model, $id);

        if (!$result) {
            throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
        }

        $this->Model->set($result);
        $this->AdminToolbar->logAction(ActionLog::READ, $this->Model, $id);

        $this->AdminToolbar->setAssociationCounts($this->Model);

        $this->set('result', $result);
        $this->overrideView('read');
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

        if ($this->overrideAction('update', $id)) {
            return;
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
                $this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to update %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)), 'is-error');
            }
        } else {
            $this->request->data = $result;
        }

        $this->set('result', $result);
        $this->overrideView('update');
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

        if ($this->overrideAction('delete', $id)) {
            return;
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
                $this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to delete %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)), 'is-error');
            }
        }

        $this->set('result', $result);
        $this->overrideView('delete');
    }

    /**
     * Query the model for a list of records that match the term.
     *
     * @throws BadRequestException
     */
    public function type_ahead() {
        if ($response = $this->overrideAction('type_ahead')) {
            return;
        }

        $this->viewClass = 'Json';
        $this->layout = 'ajax';

        if (empty($this->request->query['term'])) {
            throw new BadRequestException(__d('admin', 'Missing Query'));
        }

        $this->set('results', $this->AdminToolbar->searchTypeAhead($this->Model, $this->request->query));
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
            $this->AdminToolbar->setFlashMessage(__d('admin', '%s does not allow this process', $model->singularName), 'is-error');
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

            $this->AdminToolbar->logAction(ActionLog::PROCESS, $model, '', __d('admin', 'Triggered %s.%s() process', array($behavior, $method)));
            $this->AdminToolbar->setFlashMessage(__d('admin', 'Processed %s.%s() for %s', array($behavior, $method, mb_strtolower($model->pluralName))));

        } else {
            $this->AdminToolbar->setFlashMessage(__d('admin', '%s does not allow this process', $model->singularName), 'is-error');
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
        if (in_array($action, array('type_ahead', 'proxy', 'process_behavior', 'process_model', 'export'))) {
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

            if (!$this->Model) {
                throw new ForbiddenException(__d('admin', 'Invalid Model'));
            }
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

        // Don't validate post since data changes constantly
        $this->Security->validatePost = false;
    }

    /**
     * Override a CRUD action with an external Controller's action.
     * Request the custom action and set its response.
     *
     * @param string $action
     * @param int $id
     * @return bool
     */
    protected function overrideAction($action, $id = null) {
        $overrides = Configure::read('Admin.actionOverrides');
        $model = $this->Model->qualifiedName;

        if (empty($overrides[$model][$action])) {
            return false;
        }

        $url = (array) $overrides[$model][$action];
        $url[] = $id;

        $response = $this->requestAction($url, array(
            'autoRender' => true,
            'bare' => false,
            'return' => true,
            'override' => true,
            'data' => $this->request->data
        ));

        $this->autoRender = false;
        $this->response->body($response);

        return true;
    }

    /**
     * Override a CRUD view with an external view template.
     *
     * @param string $action
     * @return bool
     */
    protected function overrideView($action) {
        $overrides = Configure::read('Admin.viewOverrides');
        $model = $this->Model->qualifiedName;
        $view = in_array($action, array('create', 'update')) ? 'form' : $action;

        if (empty($overrides[$model][$action])) {
            $this->render($view);
            return;
        }

        $override = $overrides[$model][$action] + array(
            'action' => $view,
            'controller' => $this->name,
            'plugin' => null
        );

        // View settings
        $this->view = $override['action'];
        $this->viewPath = Inflector::camelize($override['controller']);
        $this->plugin = Inflector::camelize($override['plugin']);

        // Override
        $this->request->params['override'] = true;

        $this->render();
    }

}