<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class LogsController extends AdminAppController {

	/**
	 * Paginate the logs.
	 */
	public function index() {
		$this->paginate = array_merge(array(
			'limit' => 25,
			'order' => array('ActionLog.created' => 'DESC'),
			'contain' => array_keys($this->Model->belongsTo)
		), $this->Model->admin['paginate']);

		$this->set('results', $this->paginate($this->Model));
	}

	/**
	 * Parse and read syslogs.
	 *
	 * @param string $type
	 * @throws NotFoundException
	 * @throws BadRequestException
	 */
	public function read($type = 'debug') {
		$path = TMP . 'logs/' . $type . '.log';
		$logs = array();
		$exceptions = array();
		$message = null;
		$stack = null;

		if (!in_array($type, CakeLog::configured())) {
			throw new NotFoundException();
		}

		if (file_exists($path)) {
			if (filesize($path) > 2097152) {
				throw new BadRequestException(__('Can not read %s as it exceeds 2MB', basename($path)));
			}

			if ($file = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
				foreach ($file as $line) {
					// Stack trace
					if ($line[0] === '#') {
						$stack .= $line . PHP_EOL;

					// Error message
					} else {
						if ($stack) {
							$logs[$message]['stack'] = trim($stack);
							$stack = null;
						}

						// Parse out substrings
						preg_match('/^([0-9\-:\s]+)\s([a-z]+:)\s(?:\[([a-z]+)\])?\s?(.*?)$/i', $line, $matches);

						$date = $matches[1];
						$exception = $matches[3];
						$message = $matches[4];

						// Save mapping
						if (isset($logs[$message])) {
							$logs[$message]['count']++;
							$logs[$message]['date'] = $date;

						} else {
							$logs[$message] = array(
								'line' => $line,
								'exception' => $exception,
								'message' => $message,
								'stack' => null,
								'count' => 1,
								'date' => $date
							);
						}

						if ($exception) {
							$exceptions[$exception][] = $date;
						}
					}
				}
			}

			// Sort by count
			usort($logs, function($a, $b) {
				return $b['count'] - $a['count'];
			});
		}

		$this->set('type', $type);
		$this->set('logs', $logs);
		$this->set('exceptions', $exceptions);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Model = Admin::introspectModel('Admin.ActionLog');
	}

}