<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

$id = $result[$model->alias][$model->primaryKey];
$displayField = $this->Admin->getDisplayField($model, $result);
$dependencies = $this->Admin->getDependencies($model); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, __('Delete %s', $model->singularName)); ?></h2>

<p><?php echo __('Are you sure you want to delete %s?', $this->Html->link($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug))); ?></p>

<?php // List out dependencies as a warning
if ($dependencies) {
	$excludeDeps = array(); ?>

	<p><?php echo __('The associated records in the following models will also be deleted.'); ?></p>

	<div class="alert alert-block">
		<?php echo $this->Admin->loopDependencies($dependencies, $excludeDeps); ?>
	</div>

<?php }

// Confirm delete form
echo $this->Form->create($model->alias, array('class' => 'form-horizontal')); ?>

	<div class="well form-actions">
		<?php echo $this->element('form_actions'); ?>

		<button type="submit" class="btn btn-large btn-danger">
			<span class="icon-remove icon-white"></span>
			<?php echo __('Yes, Delete'); ?>
		</button>
	</div>

<?php echo $this->Form->end(); ?>