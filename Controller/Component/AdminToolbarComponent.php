<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');

/**
 * @property Controller $Controller
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class AdminToolbarComponent extends Component {

    /**
     * Components.
     *
     * @type array
     */
    public $components = array('Auth', 'Session');

    /**
     * Store the controller.
     *
     * @param Controller $controller
     * @throws ForbiddenException
     */
    public function startup(Controller $controller) {
        $this->Controller = $controller;

        // Thrown an exception if accessing an override action without requestAction()
        if (substr($controller->action, 0, 6) === 'admin_' && empty($controller->request->params['override'])) {
            throw new ForbiddenException();
        }

        // Set ACL session
        $user_id = $this->Auth->user('id');

        if (!$user_id || $this->Auth->user(Configure::read('User.fieldMap.status')) == Configure::read('User.statusMap.banned')) {
            return;
        }

        if (!$this->Session->check('Acl')) {
            $roles = ClassRegistry::init('Admin.RequestObject')->getRoles($user_id);
            $isAdmin = false;
            $isSuper = false;
            $roleMap = array();

            foreach ($roles as $role) {
                if (!$isAdmin && $role['RequestObject']['alias'] == Configure::read('Admin.aliases.administrator')) {
                    $isAdmin = true;
                }

                if (!$isSuper && $role['RequestObject']['alias'] == Configure::read('Admin.aliases.superModerator')) {
                    $isSuper = true;
                }

                $roleMap[$role['RequestObject']['id']] = $role['RequestObject']['alias'];
            }

            $this->Session->write('Acl.isAdmin', $isAdmin);
            $this->Session->write('Acl.isSuper', $isSuper);
            $this->Session->write('Acl.roles', $roleMap);
        }

        // Set reported count
        Configure::write('Admin.menu.reports.count', Admin::introspectModel('Admin.ItemReport')->getCountByStatus());
    }

    /**
     * An action or view is being overridden, so prepare the layout and environment.
     * Bind any other data that should be present.
     *
     * @param Controller $controller
     */
    public function beforeRender(Controller $controller) {
        $badges = array();

        foreach (Configure::read('Admin.menu') as $section => $menu) {
            $badges[$section] = $menu['count'];
        }

        $controller->set('badgeCounts', $badges);

        if (isset($controller->Model)) {
            $controller->set('model', $controller->Model);
        }

        if (isset($controller->request->params['override'])) {
            $controller->layout = 'Admin.admin';
            $controller->request->params['action'] = str_replace('admin_', '', $controller->action);
        }
    }

    /**
     * Get a list of valid containable model relations.
     * Should also get belongsTo data for hasOne and hasMany.
     *
     * @param Model $model
     * @param bool $extended
     * @return array
     */
    public function getDeepRelations(Model $model, $extended = true) {
        $contain = array_keys($model->belongsTo);
        $contain = array_merge($contain, array_keys($model->hasAndBelongsToMany));

        if ($extended) {
            foreach (array($model->hasOne, $model->hasMany) as $assocs) {
                foreach ($assocs as $alias => $assoc) {
                    $contain[$alias] = array_keys($model->{$alias}->belongsTo);
                }
            }
        }

        return $contain;
    }

    /**
     * Return a record based on ID.
     *
     * @param Model $model
     * @param int $id
     * @param bool $deepRelation
     * @return array
     */
    public function getRecordById(Model $model, $id, $deepRelation = true) {
        $model->id = $id;

        $data = $model->find('first', array(
            'conditions' => array($model->alias . '.' . $model->primaryKey => $id),
            'contain' => $this->getDeepRelations($model, $deepRelation)
        ));

        if ($data) {
            $model->set($data);
        }

        return $data;
    }

    /**
     * Return a list of records. If a certain method exists, use it.
     *
     * @param Model $model
     * @return array
     */
    public function getRecordList(Model $model) {
        if ($model->hasMethod('generateTreeList')) {
            return $model->generateTreeList(null, null, null, ' -- ');

        } else if ($model->hasMethod('getList')) {
            return $model->getList();
        }

        return $model->find('list', array(
            'order' => array($model->alias . '.' . $model->displayField => 'ASC')
        ));
    }

    /**
     * Return a count of records. If a certain method exists, use it.
     *
     * @param Model $model
     * @return array
     */
    public function getRecordCount(Model $model) {
        if ($model->hasMethod('getCount')) {
            return $model->getCount();
        }

        return $model->find('count');
    }

    /**
     * Return the request data after processing the fields.
     *
     * @return array
     */
    public function getRequestData() {
        $data = $this->Controller->request->data;

        if ($data) {
            foreach ($data as $model => $fields) {
                foreach ($fields as $key => $value) {
                    if (
                        (mb_substr($key, -5) === '_null') ||
                        (mb_substr($key, -11) === '_type_ahead') ||
                        in_array($key, array('redirect_to', 'log_comment', 'report_action'))
                    ) {
                        unset($data[$model][$key]);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Check to see if a user has specific CRUD access for a model.
     *
     * @param string $model
     * @param string $action
     * @param string $session
     * @param bool $exit - Exit early if the CRUD key doesn't exist in the session
     * @return bool
     */
    public function hasAccess($model, $action, $session = 'Admin.crud', $exit = false) {
        return Admin::hasAccess($model, $action, $session, $exit);
    }

    /**
     * Log a users action.
     *
     * @param int $action
     * @param Model $model
     * @param int $id
     * @param string $comment
     * @param string $item
     * @return bool
     * @throws InvalidArgumentException
     */
    public function logAction($action, Model $model = null, $id = null, $comment = null, $item = null) {
        if (!Configure::read('Admin.logActions')) {
            return false;
        }

        $log = ClassRegistry::init('Admin.ActionLog');
        $query = array(
            'user_id' => $this->Auth->user('id'),
            'action' => $action
        );

        // Validate action
        if (!$log->enum('action', $action)) {
            throw new InvalidArgumentException(__d('admin', 'Invalid log action type'));
        }

        if ($model) {
            $query['model'] = ($model->plugin ? $model->plugin . '.' : '') . $model->name;

            // Get comment from request
            if (!$comment && isset($this->Controller->request->data[$model->alias]['log_comment'])) {
                $comment = $this->Controller->request->data[$model->alias]['log_comment'];
            }

            // Get display field from data
            if (!$item) {
                if (isset($model->data[$model->alias][$model->displayField])) {
                    $item = $model->data[$model->alias][$model->displayField];
                }
            }
        }

        $query['foreign_key'] = (string) $id;
        $query['comment'] = $comment;
        $query['item'] = $item;

        return $log->logAction($query);
    }

    /**
     * Parse the request into an array of filtering SQL conditions.
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    public function parseFilterConditions(Model $model, $data) {
        $conditions = array();
        $fields = $model->fields;
        $alias = $model->alias;
        $enum = $model->enum;

        foreach ($data as $key => $value) {
            if (mb_substr($key, -7) === '_filter' || mb_substr($key, -11) === '_type_ahead') {
                $data[$key] = urldecode($value);
                continue;

            } else if (!isset($fields[$key])) {
                continue;
            }

            $field = $fields[$key];
            $value = urldecode($value);

            // Dates, times, numbers
            if (isset($data[$key . '_filter'])) {
                $operator = $data[$key . '_filter'];
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
        if (isset($this->Controller->request->data[$model->alias])) {
            $data = array_merge($this->Controller->request->data[$model->alias], $data);
        }

        $this->Controller->request->data[$model->alias] = $data;

        return $conditions;
    }

    /**
     * Redirect after a create or update.
     *
     * @param Model $model
     * @param string $action
     */
    public function redirectAfter(Model $model, $action = null) {
        if (!$action) {
            $action = $this->Controller->request->data[$model->alias]['redirect_to'];
        }

        if ($action === 'parent') {
            $parentName = $this->Controller->request->data[$model->alias]['redirect_to_model'];
            $className = $model->belongsTo[$parentName]['className'];
            $foreignKey = $model->belongsTo[$parentName]['foreignKey'];
            $id = !empty($this->Controller->request->data[$model->alias][$foreignKey]) ? $this->Controller->request->data[$model->alias][$foreignKey] : null;
            $foreignModel = Admin::introspectModel($className);
            $url = array('plugin' => 'admin', 'controller' => 'crud', 'action' => $id ? 'read' : 'index', $id, 'model' => $foreignModel->urlSlug);

        } else {
            $url = array('plugin' => 'admin', 'controller' => 'crud', 'action' => $action, 'model' => $model->urlSlug);

            switch ($action) {
                case 'read':
                case 'update':
                case 'delete':
                    $url[] = $model->id;
                break;
            }

        }

        $this->Controller->redirect($url);
    }

    /**
     * Report and flag an item (model and ID).
     *
     * @param int $type
     * @param Model $model
     * @param int $id
     * @param string $reason
     * @param int $user_id
     * @return bool
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function reportItem($type, Model $model, $id, $reason = null, $user_id = null) {
        $report = ClassRegistry::init('Admin.ItemReport');

        // Validate type
        if (!$report->enum('type', $type)) {
            throw new InvalidArgumentException(__d('admin', 'Invalid item report type'));
        }

        // Get model ID if null
        if (!$id) {
            $id = $model->id;
        }

        // Get logged in user ID
        if (!$user_id) {
            $user_id = $this->Auth->user('id');
        }

        // Validate record
        $result = $this->getRecordById($model, $id);

        if (!$result) {
            throw new NotFoundException();
        }

        return $report->reportItem(array(
            'reporter_id' => $user_id,
            'type' => $type,
            'model' => ($model->plugin ? $model->plugin . '.' : '') . $model->name,
            'foreign_key' => $id,
            'item' => $result[$model->alias][$model->displayField],
            'reason' => $reason
        ));
    }

    /**
     * Search for a list of records that match the query.
     *
     * @param Model $model
     * @param array $query
     * @return array
     */
    public function searchTypeAhead(Model $model, array $query) {
        if ($model->hasMethod('searchTypeAhead')) {
            return $model->searchTypeAhead($query);
        }

        $keyword = $query['term'];
        unset($query['term']);

        $results = $model->find('all', array(
            'conditions' => array($model->alias . '.' . $model->displayField . ' LIKE' => '%' . $keyword . '%') + $query,
            'order' => array($model->alias . '.' . $model->displayField => 'ASC'),
            'contain' => false
        ));
        $data = array();

        foreach ($results as $result) {
            $data[] = array(
                'id' => $result[$model->alias][$model->primaryKey],
                'title' => $result[$model->alias][$model->displayField]
            );
        }

        return $data;
    }

    /**
     * Set a count for every model association.
     *
     * @param Model $model
     */
    public function setAssociationCounts(Model $model) {
        $counts = array();

        foreach (array($model->hasMany, $model->hasAndBelongsToMany) as $property) {
            foreach ($property as $alias => $assoc) {
                $class = isset($assoc['with']) ? $assoc['with'] : $assoc['className'];

                $counts[$alias] = Admin::introspectModel($class)->find('count', array(
                    'conditions' => array($assoc['foreignKey'] => $model->id),
                    'contain' => false
                ));
            }
        }

        $this->Controller->set('counts', $counts);
    }

    /**
     * Set belongsTo data for select inputs. If there are too many records, switch to type ahead.
     *
     * @param Model $model
     */
    public function setBelongsToData(Model $model) {
        $typeAhead = array();

        foreach ($model->belongsTo as $alias => $assoc) {
            $object = Admin::introspectModel($assoc['className']);
            $count = $this->getRecordCount($object);

            // Add to type ahead if too many records
            if (!$count || $count > $object->admin['associationLimit']) {
                $class = $assoc['className'];

                if (mb_strpos($class, '.') === false) {
                    $class = Configure::read('Admin.coreName') . '.' . $class;
                }

                // Get the foreign key from the child -> parent
                $foreignKey = null;

                foreach ($object->belongsTo as $childAssoc) {
                    if ($childAssoc['className'] === $model->name) {
                        $foreignKey = $childAssoc['foreignKey'];
                        break;
                    }
                }

                $typeAhead[$assoc['foreignKey']] = array(
                    'alias' => $alias,
                    'model' => $class,
                    'foreignKey' => $foreignKey
                );

            } else {
                $variable = Inflector::variable(Inflector::pluralize(preg_replace('/(?:_id)$/', '', $assoc['foreignKey'])));

                $this->Controller->set($variable, $this->getRecordList($object));
            }
        }

        $this->Controller->set('typeAhead', $typeAhead);
    }

    /**
     * Set hasAndBelongsToMany data for forms. This allows for saving of associated data.
     *
     * @param Model $model
     */
    public function setHabtmData(Model $model) {
        foreach ($model->hasAndBelongsToMany as $assoc) {
            if (!$assoc['showInForm']) {
                continue;
            }

            $object = Admin::introspectModel($assoc['className']);
            $variable = Inflector::variable(Inflector::pluralize(preg_replace('/(?:_id)$/', '', $assoc['associationForeignKey'])));

            $this->Controller->set($variable, $this->getRecordList($object));
        }
    }

    /**
     * Convenience method to set a flash message.
     *
     * @param string $message
     * @param string $type
     */
    public function setFlashMessage($message, $type = 'is-success') {
        $this->Session->setFlash($message, 'flash', array('class' => $type));
    }

}