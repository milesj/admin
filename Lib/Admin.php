<?php

class Admin {

	/**
	 * Cached data.
	 *
	 * @var array
	 */
	protected static $_cache = array();

	/**
	 * Cache the result of the callback into the class.
	 *
	 * @param string|array $key
	 * @param callable $callback
	 * @return mixed
	 */
	public static function cache($key, Closure $callback) {
		if (is_array($key)) {
			$key = implode('-', $key);
		}

		if (isset(self::$_cache[$key])) {
			return self::$_cache[$key];
		}

		self::$_cache[$key] = $callback();

		return self::$_cache[$key];
	}

	/**
	 * Return a list of all models grouped by plugin.
	 *
	 * @return array
	 */
	public static function getModels() {
		return self::cache(__METHOD__, function() {
			$plugins = array_merge(array(Configure::read('Admin.coreName')), App::objects('plugins'));
			$map = array();

			foreach ($plugins as $plugin) {
				$data = self::getPlugin($plugin);

				if ($data['models']) {
					$map[$plugin] = $data;
				}
			}

			ksort($map);

			return $map;
		});
	}

	/**
	 * Return meta information on a plugin while also including the model list.
	 *
	 * @param string $plugin
	 * @return array
	 */
	public static function getPlugin($plugin) {
		return self::cache(array(__METHOD__, $plugin), function() use ($plugin) {
			$path = null;

			if ($plugin !== Configure::read('Admin.coreName')) {
				if (!CakePlugin::loaded($plugin)) {
					return null;
				}

				$path = CakePlugin::path($plugin);
			}

			return array(
				'title' => $plugin,
				'path' => $path,
				'slug' => Inflector::underscore($plugin),
				'models' => self::getPluginModels($plugin)
			);
		});
	}

	/**
	 * Return a list of all models within a plugin.
	 *
	 * @param string $plugin
	 * @return array
	 */
	public static function getPluginModels($plugin) {
		return self::cache(array(__METHOD__, $plugin), function() use ($plugin) {
			$search = 'Model';
			$core = Configure::read('Admin.coreName') ?: 'Core';

			if ($plugin !== $core) {
				$search = $plugin . '.' . $search;
			}

			// Fetch models and filter out AppModel's
			$models = array_filter(App::objects($search), function($value) {
				return (strpos($value, 'AppModel') === false);
			});

			// Filter out models that don't connect to the database or are admin disabled
			$map = array();
			$ignore = Configure::read('Admin.ignoreModels');

			foreach ($models as $model) {
				$class = $model;
				$id = $plugin . '.' . $model;

				if ($plugin !== $core) {
					$class = $plugin . '.' . $class;
				}

				$object = ClassRegistry::init($class);

				if (in_array($class, $ignore) || empty($object->useTable) || (isset($object->admin) && $object->admin === false)) {
					continue;
				}

				$map[] = array(
					'title' => $model,
					'class' => $class,
					'id' => $id,
					'url' => Inflector::underscore($plugin) . '.' . Inflector::underscore($model),
					'installed' => self::isModelInstalled($id)
				);
			}

			return $map;
		});
	}

	/**
	 * Check if a model has been installed into the ACO table.
	 *
	 * @param string $model
	 * @return bool
	 */
	public static function isModelInstalled($model) {
		return self::introspectModel('Admin.ControlObject')->hasAlias($model);
	}

	/**
	 * Parse a model name to extract the plugin and fully qualified name.
	 *
	 * @param string $model
	 * @return array
	 */
	public static function parseName($model) {
		return self::cache(array(__METHOD__, $model), function() use ($model) {
			list($plugin, $model) = pluginSplit($model);

			$plugin = Inflector::camelize($plugin);
			$model = Inflector::camelize($model);

			if ($plugin) {
				$class = $plugin . '.' . $model;
			} else {
				$class = $model;
			}

			return array($plugin, $model, $class);
		});
	}

	/**
	 * Introspect a model and append additional meta data.
	 *
	 * @param string $model
	 * @return Model
	 */
	public static function introspectModel($model) {
		return self::cache(array(__METHOD__, $model), function() use ($model) {
			$qualifiedName = $model;
			$core = Configure::read('Admin.coreName') ?: 'Core';

			// Trim off Core if it exists
			if (substr($model, 0, strlen($core)) === $core) {
				$model = substr($model, strlen($core) + 1, strlen($model));
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

			return $object;
		});
	}

}