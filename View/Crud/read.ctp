<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

$id = $result[$model->alias][$model->primaryKey]; ?>

<div class="buttons">
	<?php
	echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add %s', $model->singularName),
		array('action' => 'create', 'model' => $model->urlSlug),
		array('class' => 'btn btn-primary btn-large', 'escape' => false));

	echo $this->Html->link('<span class="icon-edit icon-white"></span> ' . __('Edit %s', $model->singularName),
		array('action' => 'update', $id, 'model' => $model->urlSlug),
		array('class' => 'btn btn-success btn-large', 'escape' => false));

	if ($model->admin['deletable']) {
		echo $this->Html->link('<span class="icon-remove icon-white"></span> ' . __('Delete %s', $model->singularName),
			array('action' => 'delete', $id, 'model' => $model->urlSlug),
			array('class' => 'btn btn-danger btn-large', 'escape' => false));
	} ?>
</div>

<h2><?php echo $this->Admin->getDisplayField($model, $result); ?></h2>

<div class="row-fluid">
	<table class="table table-striped table-bordered">
		<tbody>
			<?php foreach ($model->fields as $field => $data) { ?>

				<tr>
					<td class="span5">
						<b><?php echo $data['title']; ?></b>
					</td>
					<td>
						<?php echo $this->element('field', array(
							'field' => $field,
							'data' => $data,
							'value' => $result[$model->alias][$field]
						)); ?>
					</td>
				</tr>

			<?php } ?>
		</tbody>
	</table>
</div>

<?php // Loop over the types of associations
foreach (array(
	'hasOne' => 'Has One',
	'hasMany' => 'Has Many',
	'hasAndBelongsToMany' => 'Has and Belongs to Many'
) as $property => $title) {
	if ($associations = $model->{$property}) { ?>

	<div class="row-fluid">
		<h3 class="text-info"><?php echo __($title); ?></h3>

		<?php // Loop over the model relations
		foreach ($associations as $alias => $assoc) {
			if (!empty($result[$alias])) {
				echo $this->element('assoc/' . Inflector::underscore($property), array(
					'alias' => $alias,
					'assoc' => $assoc,
					'results' => $result[$alias]
				));
			}
		} ?>
	</div>

<?php } } ?>