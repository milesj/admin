<?php

/**
 * @property Model $Model
 */
class AdminAppController extends AppController {

	/**
	 * Remove parent models.
	 *
	 * @var array
	 */
	public $uses = array();

	/**
	 * Components.
	 *
	 * @var array
	 */
	public $components = array(
		'Session', 'Security', 'Cookie', 'Acl',
		'RequestHandler', 'Auth' => array(
			'authorize' => array('Controller')
		),
		'Utility.AutoLogin'
	);

	/**
	 * Helpers.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html', 'Session', 'Form', 'Time', 'Text', 'Paginator',
		'Utility.Breadcrumb', 'Admin.Admin'
	);

	/**
	 * Plugin configuration.
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * Use plugin layout.
	 *
	 * @var string
	 */
	public $layout = 'admin';

	/**
	 * Validate the user is authorized.
	 *
	 * @param array $user
	 * @return bool
	 */
	public function isAuthorized($user) {
		return true;
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->config = Configure::read('Admin');

		// Introspect the model if it is present
		if (isset($this->params['model'])) {
			list($plugin, $model) = pluginSplit($this->params['model']);

			if ($plugin) {
				$plugin = Inflector::camelize($plugin) . '.';
			}

			$name = Inflector::humanize($model);
			$model = Inflector::camelize($model);

			// Load model and unload risky behaviors
			$this->Model = ClassRegistry::init($plugin . $model);
			$this->Model->Behaviors->unload('Utility.Cacheable');

			// Use inherited enums also
			if (isset($this->Model->enum)) {
				$this->Model->enum = $this->Model->enum();
			}

			// Set convenience fields
			$fields = $this->Model->schema();

			foreach ($fields as $field => &$data) {
				$data['title'] = str_replace('Id', 'ID', Inflector::humanize(Inflector::underscore($field)));

				if (isset($this->Model->enum[$field])) {
					$data['type'] = 'enum';
				}
			}

			foreach ($this->Model->belongsTo as $belongsTo => $assoc) {
				$fields[$assoc['foreignKey']]['belongsTo'][] = array(
					'assoc' => $belongsTo,
					'model' => $assoc['className']
				);
			}

			$this->Model->singularName = $name;
			$this->Model->pluralName = Inflector::pluralize($name);
			$this->Model->fields = $fields;

			// Apply default admin settings
			$adminSettings = isset($this->Model->admin) ? $this->Model->admin : array();
			$adminSettings = array_merge($this->config['settings'], $adminSettings);

			if (!$adminSettings['deletable']) {
				$adminSettings['batchDelete'] = false;
			}

			$adminSettings['fileFields'] = array_merge($adminSettings['fileFields'], $adminSettings['imageFields']);

			$this->Model->admin = $adminSettings;
		}
	}

	/**
	 * Before render.
	 */
	public function beforeRender() {
		$this->set('user', $this->Auth->user());
		$this->set('config', $this->config);
		$this->set('model', $this->Model);
	}

}
