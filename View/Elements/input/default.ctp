<?php

$overwriteValue = isset($overwriteValue) ? $overwriteValue : null;

$params = array(
    'div' => false,
    'label' => false,
    'type' => 'text',
    'default' => isset($data['default']) ? $data['default'] : $overwriteValue,
    'empty' => ($this->action === 'index')
);

switch ($data['type']) {
    case 'text':
        $params['type'] = 'textarea';
        $params['class'] = 'input span-12';
    break;
    case 'integer':
        $params['type'] = 'number';
        $params['class'] = 'input span-2';
    break;
    case 'boolean':
    case 'bool':
        $params['type'] = 'checkbox';
        $params['div'] = 'checkbox';
    break;
    case 'enum':
        $params['type'] = 'select';
        $params['options'] = $this->Utility->enum($model->qualifiedName, $field);
        $params['class'] = 'input';
    break;
    case 'array':
    case 'list':
        $params['type'] = 'select';
        $params['class'] = 'input';
    break;
    default:
        $params['class'] = 'input span-6';
    break;
}

if ($this->action === 'index') {
    unset($params['class']);
}

echo $this->Form->input($field, $params);