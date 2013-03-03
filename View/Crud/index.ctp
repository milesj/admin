<?php
$this->Breadcrumb->add('Dashboard', array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add($model->pluralName, array('model' => $this->params['model']));

$this->Paginator->options(array(
	'url' => array('model' => $this->params['model'])
)); ?>

<div class="pull-right">
	<?php echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add %s', $model->singularName),
		array('action' => 'create', 'model' => $this->params['model']),
		array('class' => 'btn btn-primary', 'escape' => false)); ?>
</div>

<h2><?php echo $model->pluralName; ?></h2>

<?php echo $this->element('pagination'); ?>

<table class="table table-striped table-bordered table-hover sortable">
	<thead>
		<tr>
			<?php foreach ($model->fields as $field => $data) { ?>
				<th><?php echo $this->Paginator->sort($field, $data['title']); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php if ($results) {
			foreach ($results as $result) { ?>

				<tr>
					<?php foreach ($model->fields as $field => $data) { ?>

						<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
							<?php
							$value = $result[$model->alias][$field];
							$element = 'default';

							if (!empty($data['belongsTo']) && !empty($value)) {
								$element = 'belongs_to';

							} else if ($data['type'] === 'boolean' || $data['type'] === 'enum') {
								$element = $data['type'];

							} else if ($field === $model->primaryKey) {
								$element = 'id';
							}

							echo $this->element('field/' . $element, array(
								'result' => $result,
								'field' => $field,
								'value' => $value,
								'data' => $data
							)); ?>
						</td>

					<?php } ?>
				</tr>

			<?php }
		} else { ?>

		<tr>
			<td colspan="<?php echo count($model->fields); ?>" class="no-results">
				<?php echo __('No results to display'); ?>
			</td>
		</tr>

		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pagination'); ?>

<script type="text/javascript">
	$(function() {
		$('.sortable tr').click(function() {
			location.href = $(this).find('a:first').attr('href');
		});
	});
</script>