<?php
$class = null;
$options = null;

if ($data['type'] === 'integer') {
	$class = 'span1';
} else if ($data['type'] === 'string') {
	$class = 'span3';
} else if ($data['type'] === 'text') {
	$class = 'span5';
}

if (isset($model->enum[$field])) {
	$options = $model->enum[$field];
	$type = 'select';
}

echo $this->Form->input($field, array(
	'div' => false,
	'label' => false,
	'options' => $options,
	'class' => $class
));