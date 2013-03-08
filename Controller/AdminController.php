<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class AdminController extends AdminAppController {

	/**
	 * List out all models and plugins.
	 */
	public function index() {
		$plugins = Admin::getModels();
		$counts = array();

		// Gather record counts
		foreach ($plugins as $plugin) {
			foreach ($plugin['models'] as $model) {
				if ($model['installed']) {
					$counts[$model['class']] = Admin::introspectModel($model['class'])->find('count', array(
						'cache' => array($model['class'], 'count'),
						'cacheExpires' => '+24 hours'
					));
				}
			}
		}

		$this->set('plugins', $plugins);
		$this->set('counts', $counts);
	}

}