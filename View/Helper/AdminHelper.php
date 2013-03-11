<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');

class AdminHelper extends AppHelper {

	/**
	 * Helpers.
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Utility.Breadcrumb');

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

		if ($model->displayField == $model->primaryKey) {
			$displayField = '#' . $displayField;
		}

		return $displayField;
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
	 * Return a modified model object.
	 *
	 * @param string $model
	 * @return Model
	 */
	public function introspect($model) {
		return Admin::introspectModel($model);
	}

	/**
	 * Check to see if the field is an image and if it should be rendered as one.
	 *
	 * @param Model $model
	 * @param string $field
	 * @return bool
	 */
	public function isImage(Model $model, $field) {
		foreach ($model->admin['imageFields'] as $key => $value) {
			if ($key === $field) {
				return in_array($this->request->params['action'], (array) $value);

			} else if ($value === $field) {
				return true;
			}
		}

		return false;
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
			$output .= ' (' . $this->Html->tag('span', $className, array(
				'class' => 'muted'
			)) . ')';
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

		if (!$title) {
			$title = $this->Html->tag('span', '(' . __('Missing Title') . ')', array(
				'class' => 'text-warning'
			));
		}

		if ($model['iconClass']) {
			$title = $this->Html->tag('span', '&nbsp;', array(
				'class' => 'model-icon ' . $model['iconClass']
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
				$this->Breadcrumb->add(__('Add'), array('action' => 'create', 'model' => $model->urlSlug));
			break;
			case 'read':
			case 'update':
			case 'delete':
				$id = $result[$model->alias][$model->primaryKey];
				$displayField = $this->getDisplayField($model, $result);

				$this->Breadcrumb->add($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug));

				if ($action === 'update' || $action === 'delete') {
					$this->Breadcrumb->add(__(($action === 'update') ? 'Update' : 'Delete'), array('action' => $action, $id, 'model' => $model->urlSlug));
				}
			break;
		}
	}

}