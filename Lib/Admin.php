<?php

class Admin {

	/**
	 * Cache the models.
	 *
	 * @var array
	 */
	protected static $_cache = array();

	public static function getModels() {
		if (isset(self::$_cache[__METHOD__])) {
			return self::$_cache[__METHOD__];
		}

		$plugins = array_merge(array('Core'), App::objects('plugins'));
		$pluginMap = array();

		foreach ($plugins as $plugin) {
			if ($plugin === 'Admin') {
				continue;
			}

			$modelMap = array();
			$path = null;

			if ($plugin !== 'Core') {
				$path = CakePlugin::path($plugin);
			}

			if ($models = self::searchPlugin($plugin)) {
				foreach ($models as $model) {
					$url = Inflector::underscore($model);
					$class = $plugin . '.' . $model;

					if ($plugin !== 'Core') {
						$url = Inflector::underscore($plugin) . '.' . $url;
					}

					if (in_array($class, Configure::read('Admin.ignoreModels'))) {
						continue;
					}

					$modelMap[] = array(
						'title' => $model,
						'class' => $class,
						'url' => $url,
						'installed' => self::isModelInstalled($class)
					);
				}
			}

			if (!$modelMap) {
				continue;
			}

			$pluginMap[$plugin] = array(
				'title' => $plugin,
				'slug' => Inflector::underscore($plugin),
				'path' => $path,
				'models' => $modelMap
			);
		}

		self::$_cache[__METHOD__] = $pluginMap;

		return $pluginMap;
	}

	public static function isModelInstalled($model) {
		return (bool) self::introspectModel('Aco')->find('count', array(
			'conditions' => array('Aco.alias' => $model),
			'cache' => array('Aco::isModelInstalled', $model),
			'cacheExpires' => '+24 hours'
		));
	}

	public static function parseModelName($model) {
		list($plugin, $model) = pluginSplit($model);
		$plugin = Inflector::camelize($plugin);
		$model = Inflector::camelize($model);
		$pluginModel = $model;

		if ($plugin) {
			$pluginModel = $plugin . '.' . $model;
		}

		return array($plugin, $model, $pluginModel);
	}

	public static function searchPlugin($plugin = null) {
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
	 * Introspect a model and append additional meta data.
	 *
	 * @param string $model
	 * @return Model
	 */
	public static function introspectModel($model) {
		$qualifiedName = trim($model, '.');

		if (isset(self::$_cache[$qualifiedName])) {
			return self::$_cache[$qualifiedName];
		}

		$object = ClassRegistry::init($model);

		list($plugin, $model) = pluginSplit($model);

		// Override model
		$object->Behaviors->load('Containable');
		$object->Behaviors->load('Utility.Cacheable');
		$object->cacheQueries = true;
		$object->recursive = -1;

		// Inherit enums from parent classes
		if ($object->Behaviors->hasMethod('enum')) {
			$object->enum = $object->enum();
		}

		// Generate readable names
		$object->urlSlug = Inflector::underscore($qualifiedName);
		$object->qualifiedName = $qualifiedName;
		$object->singularName = Inflector::humanize(Inflector::underscore($model));
		$object->pluralName = Inflector::pluralize($object->singularName);

		// Generate a list of field (database column) data
		$fields = $object->schema();
		$hideFields = array();

		foreach ($fields as $field => &$data) {
			$data['title'] = str_replace('Id', 'ID', Inflector::humanize(Inflector::underscore($field)));

			if (isset($object->enum[$field])) {
				$data['type'] = 'enum';
			}

			// Hide counter cache and auto-date fields
			if (in_array($field, array('created', 'modified')) || substr($field, -6) === '_count') {
				$hideFields[] = $field;
			}
		}

		foreach ($object->belongsTo as $alias => $assoc) {
			$fields[$assoc['foreignKey']]['belongsTo'][$alias] = $assoc['className'];
		}

		$object->fields = $fields;

		// Apply default admin settings
		$settings = isset($object->admin) ? $object->admin : array();

		if (is_array($settings)) {
			$settings = array_merge(Configure::read('Admin.modelDefaults'), $settings);

			if (!$settings['deletable']) {
				$settings['batchDelete'] = false;
			}

			$settings['fileFields'] = array_merge($settings['fileFields'], $settings['imageFields']);
			$settings['hideFields'] = array_merge($settings['hideFields'], $hideFields);

			$object->admin = $settings;
		}

		// Update associated settings
		foreach ($object->hasAndBelongsToMany as &$assoc) {
			$assoc = array_merge(array('showInForm' => true), $assoc);
		}

		// Cache the model
		self::$_cache[$qualifiedName] = $object;

		return $object;
	}

}