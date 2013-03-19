<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class ReportsController extends AdminAppController {

	/**
	 * Paginate the reports.
	 */
	public function index() {
		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array('ItemReport.' . $this->Model->displayField => 'ASC'),
			'contain' => array_keys($this->Model->belongsTo),
			'conditions' => array('ItemReport.status' => ItemReport::PENDING)
		), $this->Model->admin['paginate']);

		$this->AdminToolbar->setBelongsToData($this->Model);
		$this->request->data['ItemReport']['status'] = ItemReport::PENDING;

		// Filters
		if (!empty($this->request->params['named'])) {
			$this->paginate['conditions'] = $this->AdminToolbar->parseFilterConditions($this->Model, $this->request->params['named']);
		}

		$this->set('model', $this->Model);
		$this->set('results', $this->paginate($this->Model));
	}

	/**
	 * View and moderate the report. Available actions determined by ACL.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 * @throws BadRequestException
	 */
	public function read($id) {
		$this->Model->id = $id;

		$result = $this->AdminToolbar->getRecordById($this->Model, $id);

		if (!$result) {
			throw new NotFoundException();
		}

		$item_id = $result['ItemReport']['foreign_key'];
		$itemModel = Admin::introspectModel($result['ItemReport']['model']);
		$itemResult = $this->AdminToolbar->getRecordById(Admin::introspectModel($result['ItemReport']['model']), $item_id);

		if ($this->request->is('post')) {
			$action = $this->request->data[$this->Model->alias]['report_action'];
			$status = ItemReport::RESOLVED;
			$messages = array();
			$title = $itemResult[$itemModel->alias][$itemModel->displayField];

			// Mark report as invalid
			if ($action === 'invalid_report') {
				$status = ItemReport::INVALID;

			// Delete the item
			} else if ($action === 'delete_item') {
				if ($this->AdminToolbar->hasAccess($itemModel, 'delete') && $itemModel->delete($item_id, true)) {
					$messages[] = __('Successfully deleted %s with ID %s', array($itemModel->singularName, $item_id));
					$this->AdminToolbar->logAction(ActionLog::DELETE, $itemModel, $item_id, __('Via item report #%s', $id), $title);
				}

			// Custom item callback
			} else if (method_exists($itemModel, $action)) {
				if ($itemModel->{$action}($item_id)) {
					$messages[] = __('Triggered %s.%s() process', array($itemModel->alias, $action));
					$this->AdminToolbar->logAction(ActionLog::UPDATE, $itemModel, $item_id, __('%s() via item report #%s', array($action, $id)), $title);
				}

			} else {
				throw new BadRequestException(__('Invalid Action'));
			}

			// Update the report
			$this->Model->save(array(
				'status' => $status,
				'resolver_id' => $this->Auth->user('id'),
				'comment' => $this->request->data['ItemReport']['log_comment']
			));

			$comment = __('Changed report status to %s', $this->Model->enum('status', $status));
			$messages[] = $comment;
			$this->AdminToolbar->logAction(ActionLog::UPDATE, $this->Model, $id, $comment);

			// Set message
			$this->AdminToolbar->setFlashMessage(implode('; ', $messages));
			$this->redirect(array('action' => 'index'));

		} else {
			$this->Model->set($result);
			$this->AdminToolbar->logAction(ActionLog::READ, $this->Model, $id);
		}

		$this->set('result', $result);
		$this->set('item', $itemResult);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Model = Admin::introspectModel('Admin.ItemReport');
	}

}