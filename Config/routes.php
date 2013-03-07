<?php

Router::connect('/admin/acl/:action/*', array('plugin' => 'admin', 'controller' => 'acl'));

Router::connect('/admin/:model/:action/*',
	array('plugin' => 'admin', 'controller' => 'crud'),
	array('model' => '[-_a-zA-Z0-9\.]+'));