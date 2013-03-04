<?php
if (empty($typeAhead[$field])) {
	echo $this->Form->input($field, array(
		'div' => false,
		'label' => false
	));

} else {
	echo $this->Form->input($field, array('type' => 'hidden'));
	echo $this->Form->input($field . '_type_ahead', array(
		'div' => false,
		'label' => false,
		'type' => 'text',
		'class' => 'span2',
		'data-provide' => 'typeahead',
		'autocomplete' => 'off',
		'value' => $this->data[$typeAhead[$field]['alias']][$model->{$typeAhead[$field]['alias']}->displayField]
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