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
				<?php if ($model->admin['batchProcess']) { ?>
					<th class="col-batch-delete">
						<input type="checkbox" id="check-all">
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
						<?php if ($model->admin['batchProcess']) { ?>

							<td class="col-batch-delete">
								<?php echo $this->Form->input('Comment.items.' . $id, array(
									'type' => 'checkbox',
									'value' => $id,
									'label' => false,
									'div' => false
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
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __d('admin', 'View')));
									}

									if ($this->Admin->hasAccess($model->qualifiedName, 'update') && $model->admin['editable']) {
										echo $this->Html->link('<span class="icon-edit"></span>',
											array('action' => 'update', $id, 'model' => $model->urlSlug),
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __d('admin', 'Edit')));
									}

									if ($this->Admin->hasAccess($model->qualifiedName, 'delete') && $model->admin['deletable']) {
										echo $this->Html->link('<span class="icon-remove"></span>',
											array('action' => 'delete', $id, 'model' => $model->urlSlug),
											array('class' => 'btn btn-mini', 'escape' => false, 'title' => __d('admin', 'Delete')));
									} ?>
								</div>
							</td>

						<?php }

						foreach ($model->fields as $field => $data) {
							echo $this->element('field_cell', array(
								'result' => $result,
								'field' => $field,
								'data' => $data
							));
						} ?>
					</tr>

				<?php }
			} else { ?>

			<tr>
				<td colspan="<?php echo count($model->fields) + $model->admin['batchProcess'] + $model->admin['actionButtons']; ?>" class="no-results">
					<?php echo __d('admin', 'No results to display'); ?>
				</td>
			</tr>

			<?php } ?>
		</tbody>
	</table>

<?php
echo $this->element('pagination');

if ($model->admin['batchProcess'] && $results) {
	$options = $this->Admin->getModelCallbacks($model);

	if ($this->Admin->hasAccess($model->qualifiedName, 'delete')) {
		$options['delete_item'] = __d('admin', 'Delete %s', $model->singularName);
	}

	if ($options) { ?>

	<div class="well actions">
		<div class="redirect-to">
			<?php echo $this->Form->input('batch_action', array(
				'div' => false,
				'options' => $options
			)); ?>
		</div>

		<?php if ($config['Admin']['logActions']) { ?>
			<div class="log-comment">
				<?php echo $this->Form->input('log_comment', array(
					'div' => false,
					'maxlength' => 255,
					'required' => true
				)); ?>
			</div>
		<?php } ?>

		<button type="submit" class="btn btn-large btn-danger">
			<span class="icon-cogs icon-white"></span>
			<?php echo __d('admin', 'Batch Process'); ?>
		</button>
	</div>

<?php } }

echo $this->Form->end();