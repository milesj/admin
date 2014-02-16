<?php
$params = array(
    'div' => false,
    'label' => false,
    'type' => 'text',
    'default' => '',
    'required' => false,
    'class' => 'input'
);

switch ($data['type']) {
    case 'enum':
    case 'boolean':
        $params['empty'] = true;
        $params['type'] = 'select';

        if ($data['type'] === 'enum') {
            $params['options'] = $this->Utility->enum($model->qualifiedName, $field);
        } else {
            $params['options'] = array(__d('admin', 'No'), __d('admin', 'Yes'));
        }
    break;
}

echo $this->Form->input($field, $params);