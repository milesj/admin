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
					$object = Admin::introspectModel($model['class']);

					if ($object->hasMethod('getCount')) {
						$count = $object->getCount();
					} else {
						$count = $object->find('count', array(
							'cache' => array($model['class'], 'count'),
							'cacheExpires' => '+24 hours'
						));
					}

					$counts[$model['class']] = $count;
				}
			}
		}

		$this->set('plugins', $plugins);
		$this->set('counts', $counts);
	}

	/**
	 * Analyze all models and output important information.
	 */
	public function models() {
		$this->set('plugins', Admin::getModels());
	}

	/**
	 * Display all configuration grouped by system.
	 */
	public function config() {
		$config = Configure::read();
		ksort($config);
		unset($config['debug']);

		$this->set('configuration', $config);
	}

	/**
	 * Handle reported items.
	 */
	public function reports() {
		$this->Model = Admin::introspectModel('Admin.ItemReport');

		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array($this->Model->alias . '.' . $this->Model->displayField => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo)
		), $this->Model->admin['paginate']);

		if ($this->request->is('post')) {
			$action = $this->request->data[$this->Model->alias]['action'];
			$count = 0;
			$deleted = array();
			$reported = array();

			foreach ($this->request->data[$this->Model->alias] as $key => $id) {
				if (!$id || in_array($key, array('action', 'log_comment'))) {
					continue;
				}

				$report = $this->Model->findById($id);

				if (!$report) {
					continue;
				}

				// Delete the item
				if ($action === 'delete' && $this->AdminToolbar->hasAccess($report['ItemReport']['model'], 'delete')) {
					$item = Admin::introspectModel($report['ItemReport']['model']);
					$item_id = $report['ItemReport']['foreign_key'];

					if ($item->delete($item_id, true)) {
						$this->AdminToolbar->logAction(ActionLog::DELETE, $item, $item_id, __('Via item report #%s', $id), $report['ItemReport']['item']);
						$deleted[] = $item_id;
					}
				}

				// Delete the report
				if ($this->Model->delete($id, true)) {
					$this->AdminToolbar->logAction(ActionLog::DELETE, $this->Model, $id);
					$reported[] = $id;
				}

				$count++;
			}

			if ($count > 0) {
				$message = array();

				if ($deleted) {
					$message[] = sprintf('Deleted item IDs: %s', implode(', ', $deleted));
				}

				if ($reported) {
					$message[] = sprintf('Deleted report IDs: %s', implode(', ', $reported));
				}

				$this->AdminToolbar->logAction(ActionLog::BATCH_PROCESS, $this->Model, null, implode(', ', $message));
				$this->setFlashMessage(__('%s %s have been processed', array($count, strtolower($this->Model->pluralName))));
			}
		}

		$this->set('model', $this->Model);
		$this->set('results', $this->paginate($this->Model));
	}

}