<?php
$options = array(
	'index' => __('List of %s', $model->pluralName),
	'create' => __('Create new %s', $model->singularName)
);

if ($this->action !== 'delete') {
	$options = array_merge(array(
		'update' => __('Continue Editing'),
		'read' => __('%s Overview', $model->singularName)
	), $options);
} ?>

<div class="redirect-to">
	<?php echo $this->Form->input('redirect_to', array(
		'div' => false,
		'options' => $options
	)); ?>
</div>

<?php if ($config['logActions']) { ?>
	<div class="log-comment">
		<?php echo $this->Form->input('log_comment', array(
			'div' => false,
			'maxlength' => 255,
			'required' => in_array($this->action, array('update', 'delete'))
		)); ?>
	</div>
<?php } ?>