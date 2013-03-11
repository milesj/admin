<?php
$params = array(
	'div' => false,
	'label' => false,
	'type' => 'text',
	'default' => '',
	'required' => false
);

switch ($data['type']) {
	case 'enum':
	case 'boolean':
		$params['empty'] = true;
		$params['type'] = 'select';

		if ($data['type'] === 'enum') {
			$params['options'] = $model->enum[$field];
		} else {
			$params['options'] = array(__('No'), __('Yes'));
		}
	break;
}

echo $this->Form->input($field, $params);