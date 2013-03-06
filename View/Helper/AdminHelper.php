<?php

App::uses('Admin', 'Admin.Lib');

class AdminHelper extends AppHelper {

	/**
	 * Helpers.
	 *
	 * @var array
	 */
	public $helpers = array('Utility.Breadcrumb');

	/**
	 * Generate a nested list of dependencies by looping and drilling down through all the model associations.
	 *
	 * @param Model $model
	 * @return array
	 */
	public function getDependencies(Model $model) {
		$dependencies = array();

		foreach (array($model->hasOne, $model->hasMany, $model->hasAndBelongsToMany) as $assocGroup) {
			foreach ($assocGroup as $assoc) {
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
					'model' => $class,
					'dependencies' => $this->getDependencies($this->introspect($class))
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
		return Admin::getModels();
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

		$output = '<ul>';

		foreach ($list as $dependent) {
			if (in_array($dependent['model'], $exclude)) {
				continue;
			}

			$exclude[] = $dependent['model'];

			$output .= sprintf('<li>%s %s</li>',
				$dependent['model'],
				$this->loopDependencies($dependent['dependencies'], $exclude));
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Set the breadcrumbs for the respective model and action.
	 *
	 * @param Model $model
	 * @param array $result
	 * @param string $action
	 */
	public function setBreadcrumbs(Model $model, $result, $action) {
		$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index'));

		if ($plugin = $model->plugin) {
			$this->Breadcrumb->add($plugin, array('controller' => 'admin', 'action' => 'index', '#' => Inflector::underscore($plugin)));
		}

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