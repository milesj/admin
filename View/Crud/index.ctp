<?php
$this->Admin->setBreadcrumbs($model, null, $this->action);
$this->Paginator->options(array(
	'url' => array_merge($this->params['named'], array('model' => $model->urlSlug))
));

echo $this->element('crud_actions'); ?>

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

				foreach ($model->fields as $field => $data) { ?>
					<th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<?php echo $this->Paginator->sort($field, $data['title']); ?>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php if ($results) {
				foreach ($results as $result) { ?>

					<tr>
						<?php if ($model->admin['batchDelete']) {
							$id = $result[$model->alias][$model->primaryKey]; ?>

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
				<td colspan="<?php echo count($model->fields) + $model->admin['batchDelete']; ?>" class="no-results">
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