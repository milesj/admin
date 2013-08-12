<?php

// Display a select menu of options
if (empty($typeAhead[$field])) {
	echo $this->Form->input($field, array(
		'div' => false,
		'label' => false,
		'empty' => ($this->action === 'index' || $data['null']),
		'class' => 'input',
		'type' => 'select'
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
		'class' => 'input input-belongs-to span-4',
		'autocomplete' => 'off',
		'value' => $value,
		'default' => $data['default']
	));

	echo $this->Form->input($field, array(
		'type' => 'hidden',
		'div' => false,
		'label' => false,
		'required' => false
	)); ?>

	<script type="text/javascript">
		window.addEvent('domready', function() {
			Admin.typeAhead(
				'<?php echo $this->Form->domId(); ?>',
				'<?php echo $this->Html->url(array('model' => Inflector::underscore($typeAhead[$field]['model']), 'action' => 'type_ahead')); ?>',
				{
					<?php if ($foreignKey = $typeAhead[$field]['foreignKey']) { echo $foreignKey; ?>: '<?php echo $model->id; ?>' <?php } ?>
				}
			);
		});
	</script>
<?php }