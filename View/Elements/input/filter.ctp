<?php
$options = null;
$type = 'text';

if (isset($model->enum[$field])) {
	$options = $model->enum[$field];
	$type = 'select';

} else if ($data['type'] === 'boolean') {
	$options = array(__('No'), __('Yes'));
	$type = 'select';
}

echo $this->Form->input($field, array(
	'div' => false,
	'label' => false,
	'type' => $type,
	'options' => $options,
	'default' => '',
	'empty' => true,
	'required' => false
));