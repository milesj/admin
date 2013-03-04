<?php
if ($this->action === 'create') {
	$pageTitle = __('Add %s', $model->singularName);
	$buttonTitle = __('Create');
	$result = null;
} else {
	$pageTitle = __('Edit %s', $model->singularName);
	$buttonTitle = __('Update');
}

$this->Admin->setBreadcrumbs($model, $result, $this->action); ?>

<h2><?php echo $pageTitle; ?></h2>

<?php
echo $this->Form->create($model->alias, array('class' => 'form-horizontal', 'file' => true));

foreach ($model->fields as $field => $data) {
	if (
		($this->action === 'create' && $field === 'id') || // hide ID for create
		in_array($field, array_merge($model->admin['hideFields'], array('created', 'modified'))) || // hide certain fields
		(substr($field, -6) === '_count')  // hide counter cache fields
	) {
		continue;
	}

	echo $this->element('input', array(
		'field' => $field,
		'data' => $data
	));
} ?>

<div class="well align-center">
	<button type="submit" class="btn btn-large btn-success">
		<span class="icon-edit icon-white"></span>
		<?php echo $buttonTitle; ?>
	</button>

	<button type="reset" class="btn btn-large btn-info">
		<span class="icon-refresh icon-white"></span>
		<?php echo __('Reset'); ?>
	</button>

	<a href="<?php echo $this->Html->url(array('action' => 'index', 'model' => $this->params['model'])); ?>" class="btn btn-large">
		<span class="icon-ban-circle"></span>
		<?php echo __('Cancel'); ?>
	</a>
</div>

<?php echo $this->Form->end(); ?>