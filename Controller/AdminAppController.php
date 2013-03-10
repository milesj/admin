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
 * @property ActionLog $ActionLog
 */
class AdminAppController extends Controller {

	/**
	 * Remove parent models.
	 *
	 * @var array
	 */
	public $uses = array('Admin.ActionLog');

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
	 * @throws UnauthorizedException
	 */
	public function isAuthorized($user = null) {
		if (!$user) {
			throw new ForbiddenException(__('Invalid User'));
		}

		if (Admin::introspectModel('Admin.RequestObject')->isAdmin($user['id'])) {
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

	/**
	 * Log a users action.
	 *
	 * @param int $action
	 * @param Model $model
	 * @param string $comment
	 * @param string $item
	 */
	public function logEvent($action, Model $model = null, $comment = null, $item = null) {
		if (!$this->config['logActions']) {
			return;
		}

		$query = array(
			'user_id' => $this->Auth->user('id'),
			'action' => $action
		);

		if ($model) {
			$id = $model->id;

			// Fetch ID from URL
			if (!$id) {
				if (isset($this->params['pass'][0])) {
					$id = $this->params['pass'][0];
				} else {
					$id = null;
				}
			}

			// Get display field from data
			if (!$item && isset($model->data[$model->alias][$model->displayField]) && $model->primaryKey !== $model->displayField) {
				$item = $model->data[$model->alias][$model->displayField];
			}

			// Get comment from request
			if (!$comment && isset($this->request->data[$model->alias]['log_comment'])) {
				$comment = $this->request->data[$model->alias]['log_comment'];
			}

			$query['model'] = $model->qualifiedName;
			$query['foreign_key'] = $id;
		}

		$query['comment'] = $comment;
		$query['item'] = $item;

		$this->ActionLog->logEvent($query);
	}

}
