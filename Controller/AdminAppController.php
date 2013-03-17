<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');
App::uses('ActionLog', 'Admin.Model');

/**
 * @property Model $Model
 * @property AdminToolbarComponent $AdminToolbar
 */
class AdminAppController extends Controller {

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
		'Session', 'Cookie', 'Acl', 'RequestHandler',
		'Auth' => array(
			'authorize' => array('Controller')
		),
		'Utility.AutoLogin', 'Admin.AdminToolbar'
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
	 * Paginate defaults.
	 *
	 * @var array
	 */
	public $paginate = array();

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
	 * @throws UnauthorizedException
	 */
	public function isAuthorized($user = null) {
		if (!$user) {
			throw new ForbiddenException(__('Invalid User'));
		}

		$aro = Admin::introspectModel('Admin.RequestObject');

		if ($aro->isAdmin($user['id'])) {
			if (!$this->Session->read('Admin.crud')) {
				$this->Session->write('Admin.crud', $aro->getCrudPermissions($user['id']));
			}

			return true;
		}

		throw new UnauthorizedException(__('Insufficient Access Permissions'));
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
