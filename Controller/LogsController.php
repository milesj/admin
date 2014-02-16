<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

class LogsController extends AdminAppController {

    /**
     * Paginate the logs.
     */
    public function index() {
        $this->paginate = array_merge(array(
            'limit' => 25,
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

        if (!in_array($type, CakeLog::configured())) {
            throw new NotFoundException(__d('admin', '%s Log Not Found', Inflector::humanize($type)));
        }

        if (file_exists($path)) {
            if (filesize($path) > 2097152) {
                throw new BadRequestException(__d('admin', 'Can not read %s as it exceeds 2MB', basename($path)));
            }

            if ($file = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
                $log = array();

                foreach ($file as $line) {
                    // Exception message
                    if (preg_match('/^([0-9\-:\s]+)\s([a-z]+:)\s(?:\[([a-z]+)\])?\s?(.*?)$/i', $line, $matches)) {
                        $exception = $matches[3];

                        // Save the previous log
                        if ($log) {
                            $key = md5($log['url'] . $log['message']);

                            if (isset($logs[$key])) {
                                $logs[$key]['count']++;
                            } else {
                                $logs[$key] = $log;
                            }
                        }

                        // Start a new log
                        $log = array(
                            'line' => $line,
                            'exception' => $exception,
                            'message' => $matches[4],
                            'stack' => array(),
                            'count' => 1,
                            'date' => $matches[1],
                            'url' => null
                        );

                        if ($exception) {
                            $exceptions[$exception][] = $matches[1];
                        }
                    // Request URL
                    } else if (preg_match('/^Request URL: (.*?)$/i', $line, $matches)) {
                        $log['url'] = $matches[1];

                    // Stack trace
                    } else if ($line[0] === '#') {
                        $log['stack'][] = $line . PHP_EOL;
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