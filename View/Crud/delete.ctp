<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

$id = $result[$model->alias][$model->primaryKey];
$displayField = $this->Admin->getDisplayField($model, $result);
$dependencies = array();

foreach (array($model->hasOne, $model->hasMany, $model->hasAndBelongsToMany) as $assocGroup) {
	foreach ($assocGroup as $assoc) {
		// hasOne, hasMany
		if (isset($assoc['dependent']) && $assoc['dependent']) {
			$dependencies[] = $assoc['className'];

		// hasAndBelongsToMany
		} else if (isset($assoc['joinTable'])) {
			$dependencies[] = $assoc['with'];
		}
	}
}

$dependencies = array_unique($dependencies); ?>

<h2><?php echo __('Delete %s', $model->singularName); ?></h2>

<p><?php echo __('Are you sure you want to delete %s?', $this->Html->link($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug))); ?></p>

<?php // List out dependencies as a warning
if ($dependencies) { ?>

	<p><?php echo __('If so, the records in the following associated models will also be deleted.'); ?></p>

	<div class="alert alert-block">
		<ul>
			<?php foreach ($dependencies as $dependent) { ?>
				<li><?php echo $dependent; ?></li>
			<?php } ?>
		</ul>
	</div>

<?php }

// Confirm delete form
echo $this->Form->create($model->alias); ?>

	<div class="well align-center">
		<button type="submit" class="btn btn-large btn-danger">
			<span class="icon-remove icon-white"></span>
			<?php echo __('Yes, Delete'); ?>
		</button>
	</div>

<?php echo $this->Form->end(); ?>