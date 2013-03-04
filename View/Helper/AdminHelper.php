<?php

App::uses('Introspect', 'Admin.Lib');

class AdminHelper extends AppHelper {

	/**
	 * Helpers.
	 *
	 * @var array
	 */
	public $helpers = array('Utility.Breadcrumb');

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
		$plugins = array_merge(array('Core'), App::objects('plugins'));
		$navigation = array();

		foreach ($plugins as $plugin) {
			if ($plugin === 'Admin') {
				continue;
			}

			if ($models = $this->getModels($plugin)) {
				foreach ($models as $model) {
					$url = Inflector::underscore($model);

					if ($plugin !== 'Core') {
						$url = Inflector::underscore($plugin) . '.' . $url;
					}

					$navigation[$plugin][] = array(
						'model' => $model,
						'url' => $url
					);
				}
			}
		}

		return $navigation;
	}

	/**
	 * Return a list of available models from the defined plugin.
	 *
	 * @param string $plugin
	 * @return array
	 */
	public function getModels($plugin = null) {
		if ($plugin) {
			$plugin = ($plugin === 'Core') ? '' : $plugin . '.';
		}

		// Fetch models and filter out AppModel's
		$models = array_filter(App::objects($plugin . 'Model'), function($value) {
			return (strpos($value, 'AppModel') === false);
		});

		// Filter out models that don't connect to the database or are admin disabled
		foreach ($models as $i => $model) {
			$object = ClassRegistry::init($plugin . $model);

			if (empty($object->useTable) || (isset($object->admin) && $object->admin === false)) {
				unset($models[$i]);
			}
		}

		return $models;
	}

	/**
	 * Return a modified model object.
	 *
	 * @param string $model
	 * @return Model
	 */
	public function introspect($model) {
		return Introspect::load($model);
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
			$this->Breadcrumb->add($plugin, array('controller' => 'admin', 'action' => 'plugin', Inflector::underscore($plugin)));
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
				$displayField = $this->Admin->getDisplayField($model, $result);

				$this->Breadcrumb->add($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug));

				if ($action === 'update' || $action === 'delete') {
					$this->Breadcrumb->add(__(($action === 'update') ? 'Update' : 'Delete'), array('action' => $action, $id, 'model' => $model->urlSlug));
				}
			break;
		}
	}

}