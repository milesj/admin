<?php
$this->Breadcrumb->add(__('Reports'), array('controller' => 'reports', 'action' => 'index'));
$this->Breadcrumb->add($this->Admin->getDisplayField($model, $result), array('controller' => 'reports', 'action' => 'read', $result[$model->alias][$model->primaryKey]));

$itemModel = $this->Admin->introspect($result[$model->alias]['model']); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $this->Admin->getDisplayField($model, $result)); ?></h2>

<div class="row-fluid">
	<?php echo $this->element('crud/read_table'); ?>
</div>

<?php // Does item still exist?
if ($item) { ?>

	<div class="row-fluid">
		<h3 class="text-info"><?php echo __('Reported %s', $itemModel->singularName); ?></h3>

		<?php echo $this->element('crud/read_table', array(
			'result' => $item,
			'model' => $itemModel
		)); ?>
	</div>

<?php }

// Show form if report is still pending
if ($result[$model->alias]['status'] == ItemReport::PENDING && $item) {
	$options = $this->Admin->getModelCallbacks($itemModel);

	if ($this->Admin->hasAccess($itemModel, 'delete')) {
		$options['delete_item'] = __('Delete %s', $itemModel->singularName);
	}

	$options['invalid_report'] = __('Mark As Invalid');

	echo $this->Form->create($model->alias, array('class' => 'form-horizontal')); ?>

	<div class="well actions">
		<div class="redirect-to">
			<?php echo $this->Form->input('report_action', array(
				'div' => false,
				'options' => $options
			)); ?>
		</div>

		<div class="log-comment">
			<?php echo $this->Form->input('log_comment', array(
				'div' => false,
				'maxlength' => 255,
				'required' => true
			)); ?>
		</div>

		<button type="submit" class="btn btn-large btn-danger">
			<span class="icon-cog icon-white"></span>
			<?php echo __('Process Report'); ?>
		</button>
	</div>

	<?php echo $this->Form->end();
}