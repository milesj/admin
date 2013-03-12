<?php
$this->Breadcrumb->add(__('Reports'), array('controller' => 'admin', 'action' => 'config'));

$this->Paginator->options(array('url' => $this->params['named'])); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $model->pluralName); ?></h2>

<?php
echo $this->Form->create($model->alias, array('class' => 'form-horizontal'));
echo $this->element('pagination'); ?>

	<table id="table" class="table table-striped table-bordered table-hover sortable clickable">
		<thead>
			<tr>
				<th class="col-batch-delete">
					<input type="checkbox" id="check-all">
				</th>
				<th class="col-id">
					<?php echo $this->Paginator->sort('id', __('ID')); ?>
				</th>
				<th class="col-model">
					<?php echo $this->Paginator->sort('model', __('Model')); ?>
				</th>
				<th class="col-item">
					<?php echo $this->Paginator->sort('item', __('Item')); ?>
				</th>
				<th class="col-comment">
					<?php echo $this->Paginator->sort('comment', __('Comment')); ?>
				</th>
				<th class="col-user_id">
					<?php echo $this->Paginator->sort('user_id', __('Reported By')); ?>
				</th>
				<th class="col-created">
					<?php echo $this->Paginator->sort('created', __('Created')); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if ($results) {
				foreach ($results as $result) {
					$id = $result[$model->alias][$model->primaryKey];
					$item_id = $result[$model->alias]['foreign_key'];
					$object = $this->Admin->introspect($result[$model->alias]['model']); ?>

					<tr>
						<td class="col-batch-delete">
							<?php echo $this->Form->input($id, array(
								'type' => 'checkbox',
								'value' => $id,
								'label' => false,
								'div' => false,
								'disabled' => !$this->Admin->hasAccess($object->qualifiedName, 'delete')
							)); ?>
						</td>

						<?php foreach (array('id', 'model') as $field) {
							$data = $model->fields[$field]; ?>

							<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
								<?php echo $this->element('field', array(
									'result' => $result,
									'field' => $field,
									'data' => $data,
									'value' => $result[$model->alias][$field]
								)); ?>
							</td>

						<?php } ?>

						<td class="col-item">
							<?php
							$title = $result[$model->alias]['item'];

							if (!$title) {
								$title = '#' . $item_id;
							}

							echo $this->Html->link($title, array(
								'controller' => 'crud',
								'action' => 'read',
								$item_id,
								'model' => $object->urlSlug
							)); ?>
						</td>

						<?php foreach (array('comment', 'user_id', 'created') as $field) {
							$data = $model->fields[$field]; ?>

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
				<td colspan="7" class="no-results">
					<?php echo __('No results to display'); ?>
				</td>
			</tr>

			<?php } ?>
		</tbody>
	</table>

<?php
echo $this->element('pagination'); ?>

<div class="well actions">
	<div class="redirect-to">
		<?php echo $this->Form->input('action', array(
			'div' => false,
			'options' => array(
				'delete' => __('Delete Items'),
				'remove' => __('Remove Reports')
			)
		)); ?>
	</div>

	<?php if ($config['logActions']) { ?>

		<div class="log-comment">
			<?php echo $this->Form->input('log_comment', array(
				'div' => false,
				'maxlength' => 255,
				'required' => true
			)); ?>
		</div>

	<?php } ?>

	<button type="submit" class="btn btn-large btn-danger" onclick="return confirm('<?php echo __('Deleting will cascade through associations, are you sure?'); ?>');">
		<span class="icon-cog icon-white"></span>
		<?php echo __('Process Reports'); ?>
	</button>
</div>

<?php echo $this->Form->end();