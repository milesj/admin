<?php
echo $this->Html->link($value, array(
	'plugin' => 'admin',
	'controller' => 'crud',
	'action' => 'read',
	'model' => $this->params['model'],
	$value
));