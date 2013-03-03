<?php
$value = $result[$model->alias][$field];
$element = 'default';

if ($value === null || $value === '') { ?>

	<div class="muted align-center">-</div>

<?php } else if (!empty($data['belongsTo']) && !empty($value)) {
	$element = 'belongs_to';

} else if ($data['type'] === 'boolean' || $data['type'] === 'enum') {
	$element = $data['type'];

} else if ($field === $model->primaryKey) {
	$element = 'id';

} else if (in_array($field, $model->admin['imageFields'])) {
	$element = 'image';
}

echo $this->element('field/' . $element, array(
	'result' => $result,
	'field' => $field,
	'value' => $value,
	'data' => $data
));