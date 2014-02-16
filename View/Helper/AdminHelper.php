<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');

class AdminHelper extends AppHelper {

    /**
     * Helpers.
     *
     * @type array
     */
    public $helpers = array('Html', 'Session', 'Utility.Breadcrumb');

    /**
     * Filter down fields if the association has a whitelist.
     *
     * @param Model $model
     * @param array $filter
     * @return array|mixed
     */
    public function filterFields(Model $model, $filter = array()) {
        if (empty($filter) || $filter === '*') {
            return $model->fields;
        }

        $fields = array();

        foreach ($filter as $field) {
            list($table, $field) = pluginSplit($field);

            $fields[$field] = $model->fields[$field];
        }

        return $fields;
    }

    /**
     * Return a list of valid callbacks for the model + behaviors and logged in user.
     *
     * @param Model $model
     * @return array
     */
    public function getBehaviorCallbacks(Model $model) {
        $callbacks = array();
        $behaviors = Configure::read('Admin.behaviorCallbacks');

        foreach ($behaviors as $behavior => $methods) {
            if (!$model->Behaviors->loaded($behavior)) {
                continue;
            }

            foreach ($methods as $method => $options) {
                if (is_string($options)) {
                    $options = array('title' => $options, 'access' => 'update');
                } else {
                    $options = $options += array('access' => 'update');
                }

                if ($this->hasAccess($model->qualifiedName, $options['access'])) {
                    $callbacks[$method] = array(
                        'title' => __d('admin', $options['title'], $model->singularName),
                        'behavior' => Inflector::underscore($behavior),
                        'method' => $method
                    );
                }
            }
        }

        return $callbacks;
    }

    /**
     * Generate a nested list of dependencies by looping and drilling down through all the model associations.
     *
     * @param Model $model
     * @param int $depth
     * @return array
     */
    public function getDependencies(Model $model, $depth = 0) {
        $dependencies = array();

        if ($depth >= 5) {
            return $dependencies;
        }

        foreach (array($model->hasOne, $model->hasMany, $model->hasAndBelongsToMany) as $assocGroup) {
            foreach ($assocGroup as $alias => $assoc) {
                // hasOne, hasMany
                if (isset($assoc['dependent']) && $assoc['dependent']) {
                    $class = $assoc['className'];

                // hasAndBelongsToMany
                } else if (isset($assoc['joinTable'])) {
                    $class = $assoc['with'];

                } else {
                    continue;
                }

                $dependencies[] = array(
                    'alias' => $alias,
                    'model' => $class,
                    'dependencies' => $this->getDependencies($this->introspect($class), ($depth + 1))
                );
            }
        }

        return $dependencies;
    }

    /**
     * Return the display field. If field does not exist, use the ID.
     *
     * @param Model $model
     * @param array $result
     * @return string
     */
    public function getDisplayField(Model $model, $result) {
        $displayField = $result[$model->alias][$model->displayField];

        if (!$displayField) {
            $displayField = $this->Html->tag('span', '(' . __d('admin', 'No Title') . ')', array(
                'class' => 'text-warning'
            ));
        }

        return $displayField;
    }

    /**
     * Return a list of valid callbacks for the model and logged in user.
     *
     * @param Model $model
     * @param string $scope
     * @return array
     */
    public function getModelCallbacks(Model $model, $scope = '*') {
        $callbacks = array();
        $models = Configure::read('Admin.modelCallbacks');

        if (isset($models[$model->qualifiedName])) {
            foreach ($models[$model->qualifiedName] as $method => $options) {
                if (is_string($options)) {
                    $options = array('title' => $options, 'access' => 'update', 'scope' => '*');
                } else {
                    $options = $options += array('access' => 'update', 'scope' => '*');
                }

                if ($options['scope'] !== $scope) {
                    continue;
                }

                if ($this->hasAccess($model->qualifiedName, $options['access'])) {
                    $callbacks[$method] = __d('admin', $options['title'], $model->singularName);
                }
            }
        }

        return $callbacks;
    }

    /**
     * Return a list of valid links for a record
     *
     * @param Model $model
     * @return array
     */
    public function getModelLinks(Model $model) {
        $models = Configure::read('Admin.modelLinks');

        if (isset($models[$model->qualifiedName])) {
            return $models[$model->qualifiedName];
        }

        return array();
    }

    /**
     * Return a list of models grouped by plugin, to use in the navigation menu.
     *
     * @return array
     */
    public function getNavigation() {
        $plugins = Admin::getModels();
        $navigation = array();

        foreach ($plugins as $plugin) {
            $models = array();

            foreach ($plugin['models'] as $model) {
                if ($model['installed']) {
                    $models[Inflector::humanize($model['group'])][] = $model;
                }
            }

            $navigation[$plugin['title']] = $models;
        }

        return $navigation;
    }

    /**
     * Return a parse user route.
     *
     * @param string $route
     * @param array $user
     * @return string
     */
    public function getUserRoute($route, array $user) {
        $route = Configure::read('User.routes.' . $route);

        if (!$route) {
            return null;
        }

        $route = (array) $route;

        foreach ($route as &$value) {
            if ($value === '{id}') {
                $value = $user['id'];

            } else if ($value === '{slug}' && isset($user['slug'])) {
                $value = $user['slug'];

            } else if ($value === '{username}') {
                $value = $user[Configure::read('User.fieldMap.username')];
            }
        }

        return $this->url($route);
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
     * Check to see if the user has a role.
     *
     * @param int|string $role
     * @return bool
     */
    public function hasRole($role) {
        $roles = (array) $this->Session->read('Acl.roles');

        if (is_numeric($role)) {
            return isset($roles[$role]);
        }

        return in_array($role, $roles);
    }

    /**
     * Return a modified model object.
     *
     * @param string $model
     * @return Model
     */
    public function introspect($model) {
        return Admin::introspectModel($model);
    }

    /**
     * Return true if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin() {
        return (bool) $this->Session->read('Acl.isAdmin');
    }

    /**
     * Return true if the user is a super mod.
     *
     * @return bool
     */
    public function isSuper() {
        return ($this->isAdmin() || $this->Session->read('Acl.isSuper'));
    }

    /**
     * Generate a nested list of deletion model dependencies.
     *
     * @param array $list
     * @param array $exclude
     * @return string
     */
    public function loopDependencies($list, &$exclude = array()) {
        if (!$list) {
            return null;
        }

        $output = '';

        foreach ($list as $dependent) {
            if (in_array($dependent['model'], $exclude)) {
                continue;
            }

            $exclude[] = $dependent['model'];

            $output .= sprintf('<li>%s (%s) %s</li>',
                $dependent['model'],
                $dependent['alias'],
                $this->loopDependencies($dependent['dependencies'], $exclude));
        }

        return $this->Html->tag('ul', $output);
    }

    /**
     * Normalize an array by grabbing the keys from a multi-dimension array (belongsTo, actsAs, etc).
     *
     * @param array $array
     * @param bool $sort
     * @return array
     */
    public function normalizeArray($array, $sort = true) {
        $output = array();

        if ($array) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $output[] = $value;
                } else {
                    $output[] = $key;
                }
            }

            if ($sort) {
                sort($output);
            }
        }

        return $output;
    }

    /**
     * Output an association alias and class name. If both are equal, only display the alias.
     *
     * @param Model|array $model
     * @param string $alias
     * @param string $className
     * @return string
     */
    public function outputAssocName($model, $alias, $className) {
        $output = $this->outputIconTitle($model, $alias);

        if ($className != $alias) {
            $output .= sprintf(' (%s)',
                $this->Html->tag('span', $className, array('class' => 'text-muted'))
            );
        }

        return $output;
    }

    /**
     * Output a model title and icon if applicable.
     *
     * @param Model|array $model
     * @param string $title
     * @return string
     */
    public function outputIconTitle($model, $title = null) {
        if ($model instanceof Model) {
            $model = $model->admin;
        }

        if (!$title && isset($model['title'])) {
            $title = $model['title'];
        }

        if ($model['iconClass']) {
            $title = $this->Html->tag('span', '&nbsp;', array(
                'class' => 'model-icon fa ' . str_replace('icon-', 'fa-', $model['iconClass']) // FontAwesome 4.0 fix
            )) . ' ' . $title;
        }

        return $title;
    }

    /**
     * Set the breadcrumbs for the respective model and action.
     *
     * @param Model $model
     * @param array $result
     * @param string $action
     */
    public function setBreadcrumbs(Model $model, $result, $action) {
        list($plugin, $alias) = pluginSplit($model->qualifiedName);

        $this->Breadcrumb->add($plugin, array('controller' => 'admin', 'action' => 'index', '#' => Inflector::underscore($plugin)));
        $this->Breadcrumb->add($model->pluralName, array('controller' => 'crud', 'action' => 'index', 'model' => $model->urlSlug));

        switch ($action) {
            case 'create':
                $this->Breadcrumb->add(__d('admin', 'Add'), array('action' => 'create', 'model' => $model->urlSlug));
            break;
            case 'read':
            case 'update':
            case 'delete':
                $id = $result[$model->alias][$model->primaryKey];
                $displayField = $this->getDisplayField($model, $result);

                $this->Breadcrumb->add($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug));

                if ($action === 'update' || $action === 'delete') {
                    $this->Breadcrumb->add(__d('admin', ($action === 'update') ? 'Update' : 'Delete'), array('action' => $action, $id, 'model' => $model->urlSlug));
                }
            break;
        }
    }

}