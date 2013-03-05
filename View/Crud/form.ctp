<?php
if ($this->action === 'create') {
	$pageTitle = __('Add %s', $model->singularName);
	$buttonTitle = __('Create');
	$result = null;
} else {
	$pageTitle = __('Edit %s', $model->singularName);
	$buttonTitle = __('Update');
}

$this->Admin->setBreadcrumbs($model, $result, $this->action);

echo $this->element('action_buttons'); ?>

<h2><?php echo $pageTitle; ?></h2>

<?php
echo $this->Form->create($model->alias, array('class' => 'form-horizontal', 'file' => true));

foreach ($model->fields as $field => $data) {
	if (($this->action === 'create' && $field === 'id') || in_array($field, $model->admin['hideFields'])) {
		continue;
	}

	echo $this->element('input', array(
		'field' => $field,
		'data' => $data
	));
} ?>

<div class="well align-center">
	<div class="form-redirect-to">
		<?php echo $this->Form->input('redirect_to', array(
			'div' => false,
			'options' => array(
				'update' => __('Continue Editing'),
				'index' => __('List of %s', $model->pluralName),
				'create' => __('Create new %s', $model->singularName),
				'read' => __('%s Overview', $model->singularName)
			)
		)); ?>
	</div>

	<button type="submit" class="btn btn-large btn-success">
		<span class="icon-edit icon-white"></span>
		<?php echo $buttonTitle; ?>
	</button>

	<button type="reset" class="btn btn-large btn-info">
		<span class="icon-refresh icon-white"></span>
		<?php echo __('Reset'); ?>
	</button>

	<a href="<?php echo $this->Html->url(array('action' => 'index', 'model' => $model->urlSlug)); ?>" class="btn btn-large">
		<span class="icon-ban-circle"></span>
		<?php echo __('Cancel'); ?>
	</a>
</div>

<?php echo $this->Form->end(); ?>