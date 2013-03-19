<?php
$this->Admin->setBreadcrumbs($model, null, $this->action);
$this->Paginator->options(array(
	'url' => array_merge($this->params['named'], array('model' => $model->urlSlug))
));

echo $this->element('crud/actions'); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $model->pluralName); ?></h2>

<?php
echo $this->element('filters');

echo $this->Form->create($model->alias, array('class' => 'form-horizontal'));
echo $this->element('pagination'); ?>

	<table id="table" class="table table-striped table-bordered table-hover sortable clickable">
		<thead>
			<tr>
				<?php if ($model->admin['batchDelete']) { ?>
					<th class="col-batch-delete">
						<?php if ($this->Admin->hasAccess($model->qualifiedName, 'delete')) { ?>
							<input type="checkbox" id="check-all">
						<?php } ?>
					</th>
				<?php }

				if ($model->admin['actionButtons']) { ?>
					<th class="col-actions">-</th>
				<?php }

				foreach ($model->fields as $field => $data) { ?>
					<th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<?php echo $this->Paginator->sort($field, $data['title']); ?>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php if ($results) {
				foreach ($results as $result) {
					$id = $result[$model->alias][$model->primaryKey]; ?>

					<tr>
						<?php if ($model->admin['batchDelete']) { ?>

							<td class="col-batch-delete">
								<?php echo $this->Form->input($id, array(
									'type' => 'checkbox',
									'value' => $id,
									'label' => false,
									'div' => false,
									'disabled' => !$this->Admin->hasAccess($model->qualifiedName, 'delete')
								)); ?>
							</td>

						<?php }

						if ($model->admin['actionButtons']) { ?>

							<td class="col-actions">
								<div class="btn-group">
									<?php
									if ($this->Admin->hasAccess($model->qualifiedName, 'read')) {
										echo $this->Html->link('<span class="icon-search"></span>',
											array('action' => 'read', $id, 'model' => $model->urlSlug),
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __('View')));
									}

									if ($this->Admin->hasAccess($model->qualifiedName, 'update') && $model->admin['editable']) {
										echo $this->Html->link('<span class="icon-edit"></span>',
											array('action' => 'update', $id, 'model' => $model->urlSlug),
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __('Edit')));
									}

									if ($this->Admin->hasAccess($model->qualifiedName, 'delete') && $model->admin['deletable']) {
										echo $this->Html->link('<span class="icon-remove"></span>',
											array('action' => 'delete', $id, 'model' => $model->urlSlug),
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __('Delete')));
									} ?>
								</div>
							</td>

						<?php }

						foreach ($model->fields as $field => $data) { ?>

							<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
								<?php echo $this->element('field', array(
									'result' => $result,
									'field' => $field,
									'data' => $data,
									'value' => $result[$model->alias][$field]
								)); ?>
							</td>

						<?php } ?>
					</tr>

				<?php }
			} else { ?>

			<tr>
				<td colspan="<?php echo count($model->fields) + $model->admin['batchDelete'] + $model->admin['actionButtons']; ?>" class="no-results">
					<?php echo __('No results to display'); ?>
				</td>
			</tr>

			<?php } ?>
		</tbody>
	</table>

<?php
echo $this->element('pagination');

if ($model->admin['batchDelete'] && $results && $this->Admin->hasAccess($model->qualifiedName, 'delete')) { ?>

	<div class="well actions">
		<button type="submit" class="btn btn-large btn-danger" onclick="return confirm('<?php echo __('Deleting will cascade through associations, are you sure?'); ?>');">
			<span class="icon-trash icon-white"></span>
			<?php echo __('Batch Delete'); ?>
		</button>
	</div>

<?php }

echo $this->Form->end();