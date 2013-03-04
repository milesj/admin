<?php

class Introspect {

	/**
	 * Cache the models.
	 *
	 * @var array
	 */
	protected static $_cache = array();

	/**
	 * Introspect a model and append additional meta data.
	 *
	 * @param string $model
	 * @return Model
	 */
	public static function load($model) {
		if (isset(self::$_cache[$model])) {
			return self::$_cache[$model];
		}

		$object = ClassRegistry::init($model);
		$qualifiedName = trim($model, '.');

		list($plugin, $model) = pluginSplit($model);

		// Alter the current behaviors
		$object->Behaviors->load('Containable');
		$object->Behaviors->unload('Utility.Cacheable');

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
			$fields[$assoc['foreignKey']]['belongsTo'][] = array(
				'alias' => $alias,
				'model' => $assoc['className']
			);
		}

		$object->fields = $fields;

		// Apply default admin settings
		$settings = isset($object->admin) ? $object->admin : array();
		$settings = array_merge(Configure::read('Admin.settings'), $settings);

		if (!$settings['deletable']) {
			$settings['batchDelete'] = false;
		}

		$settings['fileFields'] = array_merge($settings['fileFields'], $settings['imageFields']);
		$settings['hideFields'] = array_merge($settings['hideFields'], $hideFields);

		$object->admin = $settings;
		self::$_cache[$model] = $object;

		return $object;
	}

}