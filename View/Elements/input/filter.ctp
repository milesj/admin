<?php
$options = null;
$params = array(
	'div' => false,
	'label' => false,
	'type' => 'text',
	'default' => '',
	'required' => false
);

if (isset($model->enum[$field])) {
	$options = $model->enum[$field];

} else if ($data['type'] === 'boolean') {
	$options = array(__('No'), __('Yes'));
}

if ($options) {
	$params['empty'] = true;
	$params['type'] = 'select';
	$params['options'] = $options;
}

echo $this->Form->input($field, $params);