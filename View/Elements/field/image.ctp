<?php

$useFile = false;

// Always allow on read action
if ($this->action === 'read') {
	$useFile = false;

// If image is not allowed in this action, display a link
} else if (isset($model->admin['imageFields'][$field])) {
	if (!in_array($this->action, (array) $model->admin['imageFields'][$field])) {
		$useFile = true;
	}

// Validate extension whitelist
} else if (!in_array(strtolower(pathinfo($value,  PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif'))) {
	$useFile = true;

// Validate image size
} else if (!empty($result[$model->alias]['width'])) {
	if ($result[$model->alias]['width'] > 200) {
		$useFile = true;
	}

// Cant trust it!
} else {
	$useFile = true;
}

// Display file link
if ($useFile) {
	echo $this->element('Admin.field/file', array(
		'result' => $result,
		'field' => $field,
		'data' => $data,
		'value' => $value,
		'model' => $model
	));

// Display image
} else {
	echo $this->Html->image($value, array(
		'pathPrefix' => '',
		'alt' => '',
		'class' => 'image'
	));
}