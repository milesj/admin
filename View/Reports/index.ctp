<?php
$this->Breadcrumb->add(__('Reports'), array('controller' => 'reports', 'action' => 'index'));

$this->Paginator->options(array('url' => $this->params['named']));

if ($this->request->data[$model->alias]['status'] == ItemReport::PENDING) {
	$fieldsToShow = array('id', 'status', 'model', 'item', 'reporter_id', 'reason', 'created');
	$pageTitle = __('Pending Reports');
} else {
	$fieldsToShow = array('id', 'status', 'model', 'item', 'resolver_id', 'comment', 'created', 'modified');
	$pageTitle = __('Resolved Reports');
}  ?>

<div class="action-buttons">
	<?php
	echo $this->Html->link('<span class="icon-filter icon-white"></span> ' . __('Filter'),
		'javascript:;', array('class' => 'btn btn-large', 'escape' => false, 'onclick' => "$('#filters').toggle()"));
	?>
</div>

<h2><?php echo $this->Admin->outputIconTitle($model, $pageTitle); ?></h2>

<?php
echo $this->element('filters');

echo $this->Form->create($model->alias, array('class' => 'form-horizontal'));
echo $this->element('pagination'); ?>

	<table id="table" class="table table-striped table-bordered table-hover sortable clickable">
		<thead>
			<tr>
				<?php foreach ($fieldsToShow as $field) { ?>
					<th class="col-<?php echo $field; ?>">
						<?php echo $this->Paginator->sort($field, $model->fields[$field]['title']); ?>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php if ($results) {
				foreach ($results as $result) {
					$id = $result[$model->alias][$model->primaryKey];
					$item_id = $result[$model->alias]['foreign_key'];
					$object = $this->Admin->introspect($result[$model->alias]['model']); ?>

					<tr>
						<?php foreach ($fieldsToShow as $field) {
							$data = $model->fields[$field];

							if ($field === 'item') { ?>

								<td class="col-item">
									<?php
									$title = $result[$model->alias]['item'];

									if (!$title) {
										$title = '#' . $item_id;
									}

									echo $this->Html->link($title, array(
										'action' => 'read',
										$item_id
									)); ?>
								</td>

							<?php } else { ?>

								<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
									<?php echo $this->element('field', array(
										'result' => $result,
										'field' => $field,
										'data' => $data,
										'value' => $result[$model->alias][$field]
									)); ?>
								</td>

							<?php }
						} ?>

					</tr>

				<?php }
			} else { ?>

			<tr>
				<td colspan="<?php echo count($fieldsToShow); ?>" class="no-results">
					<?php echo __('No results to display'); ?>
				</td>
			</tr>

			<?php } ?>
		</tbody>
	</table>

<?php
echo $this->element('pagination');
echo $this->Form->end();