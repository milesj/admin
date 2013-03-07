<?php

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