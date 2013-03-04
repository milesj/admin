<?php
$this->Admin->setBreadcrumbs($model, null, $this->action);
$this->Paginator->options(array(
	'url' => array('model' => $model->urlSlug)
)); ?>

<div class="buttons">
	<?php echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add %s', $model->singularName),
		array('action' => 'create', 'model' => $model->urlSlug),
		array('class' => 'btn btn-primary btn-large', 'escape' => false)); ?>
</div>

<h2><?php echo $model->pluralName; ?></h2>

<?php
echo $this->Form->create($model->alias);
echo $this->element('pagination'); ?>

	<table id="table" class="table table-striped table-bordered table-hover sortable clickable">
		<thead>
			<tr>
				<?php if ($model->admin['batchDelete']) { ?>
					<th class="col-batch-delete">
						<input type="checkbox" id="check-all">
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
									'div' => false
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

if ($model->admin['batchDelete'] && $results) { ?>

	<div class="well align-center">
		<button type="submit" class="btn btn-large btn-danger" onclick="return confirm('<?php echo __('Are you sure? This can not be reversed.'); ?>');">
			<span class="icon-remove-sign icon-white"></span>
			<?php echo __('Batch Delete'); ?>
		</button>
	</div>

<?php }

echo $this->Form->end();