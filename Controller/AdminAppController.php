<?php

App::uses('Admin', 'Admin.Lib');

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
		'Session', 'Cookie', 'Acl', 'RequestHandler', 'Utility.AutoLogin',
		'Auth' => array(
			'authorize' => array('Controller')
		)
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
	 * @throws ForbiddenException
	 */
	public function isAuthorized($user) {
		return true;
		if (empty($this->params['model']) || $this->name === 'Acl') {
			return true;
		}

		list($plugin, $model, $pluginModel) = Admin::parseModelName($this->params['model']);
		$action = $this->action;

		if (!in_array($action, array('create', 'update', 'delete'))) {
			$action = 'read';
		}

		if ($this->Acl->check(array('User' => $user), $pluginModel, $action)) {
			return true;
		}

		throw new ForbiddenException();
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->config = Configure::read('Admin');
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
