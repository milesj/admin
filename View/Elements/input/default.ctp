<?php
$params = array(
	'div' => false,
	'label' => false,
	'type' => 'text',
	'default' => isset($data['default']) ? $data['default'] : '',
	'empty' => ($this->action === 'index')
);

switch ($data['type']) {
	case 'string':
		$params['class'] = 'span3';
	break;
	case 'text':
		$params['class'] = 'span5';
		$params['type'] = 'textarea';
	break;
	case 'integer':
		$params['class'] = 'span1';
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