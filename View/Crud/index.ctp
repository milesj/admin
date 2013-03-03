<?php
$batchDelete = ($model->admin['batchDelete'] && $model->admin['deletable']);

$this->Breadcrumb->add('Dashboard', array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add($model->pluralName, array('model' => $this->params['model']));

$this->Paginator->options(array(
	'url' => array('model' => $this->params['model'])
)); ?>

<div class="buttons">
	<?php echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add %s', $model->singularName),
		array('action' => 'create', 'model' => $this->params['model']),
		array('class' => 'btn btn-primary btn-large', 'escape' => false)); ?>
</div>

<h2><?php echo $model->pluralName; ?></h2>

<?php
echo $this->Form->create($model->alias);
echo $this->element('pagination'); ?>

	<table id="table" class="table table-striped table-bordered table-hover sortable">
		<thead>
			<tr>
				<?php if ($batchDelete) { ?>
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
				foreach ($results as $result) {
					$id = $result[$model->alias][$model->primaryKey]; ?>

					<tr>
						<?php if ($model->admin['batchDelete']) { ?>
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
									'data' => $data
								)); ?>
							</td>

						<?php } ?>
					</tr>

				<?php }
			} else { ?>

			<tr>
				<td colspan="<?php echo count($model->fields) + $batchDelete; ?>" class="no-results">
					<?php echo __('No results to display'); ?>
				</td>
			</tr>

			<?php } ?>
		</tbody>
	</table>

<?php
echo $this->element('pagination');

if ($batchDelete && $results) { ?>

	<div class="well align-center">
		<button type="submit" class="btn btn-large btn-danger" onclick="return confirm('<?php echo __('Are you sure? This can not be reversed.'); ?>');">
			<span class="icon-remove-sign icon-white"></span>
			<?php echo __('Batch Delete'); ?>
		</button>
	</div>

<?php }

echo $this->Form->end(); ?>

<script type="text/javascript">
	$(function() {
		$('.sortable tbody tr').click(function() {
			location.href = $(this).find('a:first').attr('href');
		});

		$('#check-all').click(function() {
			$('#table input:checkbox').prop('checked', $(this).prop('checked'));
		});
	});
</script>