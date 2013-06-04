<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class UploadController extends AdminAppController {

	/**
	 * Upload a file and set transport and transform settings.
	 */
	public function index() {
		if ($this->request->is('post')) {
			$this->request->data['FileUpload']['user_id'] = $this->Auth->user('id');

			debug($this->request->data);
		} else {
			$this->request->data['FileUpload'] = Configure::read('Admin.uploads');
		}
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Model = Admin::introspectModel('Admin.FileUpload');
	}

}