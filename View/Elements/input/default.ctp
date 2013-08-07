<?php
$params = array(
	'div' => false,
	'label' => false,
	'type' => 'text',
	'class' => 'form-control',
	'default' => isset($data['default']) ? $data['default'] : '',
	'empty' => ($this->action === 'index')
);

switch ($data['type']) {
	case 'text':
		$params['type'] = 'textarea';
	break;
	case 'integer':
		$params['type'] = 'number';
	break;
	case 'boolean':
	case 'bool':
		$params['type'] = 'checkbox';
	break;
	case 'enum':
		$params['type'] = 'select';
		$params['options'] = $this->Utility->enum($model->qualifiedName, $field);
	break;
	case 'array':
	case 'list':
		$params['type'] = 'select';
	break;
}

if ($this->action === 'index') {
	unset($params['class']);
}

echo $this->Form->input($field, $params);