<?php

// Display a select menu of options
if (empty($typeAhead[$field])) {
	echo $this->Form->input($field, array(
		'div' => false,
		'label' => false,
		'empty' => ($this->action === 'index' || $data['null'])
	));

// Use a text box with type ahead via AJAX
} else {
	$value = null;
	$alias = $typeAhead[$field]['alias'];

	if (isset($this->data[$alias][$model->{$alias}->displayField])) {
		$value = $this->data[$alias][$model->{$alias}->displayField];
	}

	echo $this->Form->input($field . '_type_ahead', array(
		'div' => false,
		'label' => false,
		'type' => 'text',
		'class' => 'span2 belongs-to',
		'data-provide' => 'typeahead',
		'autocomplete' => 'off',
		'value' => $value,
		'default' => $data['default']
	));

	echo $this->Form->input($field, array(
		'type' => 'text',
		'div' => false,
		'label' => false,
		'style' => 'display: none'
	)); ?>

	<script type="text/javascript">
		$(function() {
			Admin.typeAhead(
				'<?php echo $this->Form->domId(); ?>',
				'<?php echo $this->Html->url(array('model' => Inflector::underscore($typeAhead[$field]['model']), 'action' => 'type_ahead')); ?>'
			);
		});
	</script>
<?php }