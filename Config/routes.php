<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

Router::connect('/admin/acl/:action/*', array('plugin' => 'admin', 'controller' => 'acl'));

Router::connect('/admin/:model/:action/*',
	array('plugin' => 'admin', 'controller' => 'crud'),
	array('model' => '[-_a-zA-Z0-9\.]+'));