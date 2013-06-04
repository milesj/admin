<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class FileUpload extends AdminAppModel {

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => USER_MODEL
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-upload',
		'fileFields' => array('path', 'path_thumb', 'path_large'),
		'hideFields' => array('path_thumb', 'path_large', 'size', 'ext', 'type', 'width', 'height')
	);

	/**
	 * Define Uploader Attachment configuration dynamically.
	 *
	 * @param bool $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		if (CakePlugin::loaded('Uploader')) {
			$this->actsAs['Uploader.Attachment'] = array(
				'path' => array(
					//'nameCallback' => 'formatName',
					'overwrite' => true,
					'metaColumns' => array(
						'size' => 'size',
						'ext' => 'ext',
						'type' => 'type',
						'height' => 'width',
						'width' => 'height'
					),
					'transforms' => Configure::read('Admin.uploads.transforms'),
					'transport' => Configure::read('Admin.uploads.transport')
				)
			);

			$this->actsAs['Uploader.FileValidation'] = array(
				'path' => Configure::read('Admin.uploads.validation')
			);
		}

		parent::__construct($id, $table, $ds);
	}

	/**
	 * Format the file name.
	 *
	 * @param string $name
	 * @param \Transit\File $file
	 * @return string
	 */
	public function formatName($name, $file) {
		return md5($name . time());
	}

}