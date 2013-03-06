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
		$qualifiedName = trim($model, '.');

		if (isset(self::$_cache[$qualifiedName])) {
			return self::$_cache[$qualifiedName];
		}

		$object = ClassRegistry::init($model);

		list($plugin, $model) = pluginSplit($model);

		// Override model
		$object->Behaviors->load('Containable');
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