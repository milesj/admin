<?php
$displayField = $id = $result[$model->alias][$model->primaryKey];

if (isset($result[$model->alias][$model->displayField])) {
	$displayField = $result[$model->alias][$model->displayField];
}

$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add($model->pluralName, array('action' => 'index', 'model' => $this->params['model']));
$this->Breadcrumb->add($displayField, array('action' => 'read', $id, 'model' => $this->params['model'])); ?>

<div class="buttons">
	<?php
	echo $this->Html->link('<span class="icon-edit icon-white"></span> ' . __('Edit %s', $model->singularName),
		array('action' => 'update', $id, 'model' => $this->params['model']),
		array('class' => 'btn btn-success btn-large', 'escape' => false));

	if ($model->admin['deletable']) {
		echo $this->Html->link('<span class="icon-remove icon-white"></span> ' . __('Delete %s', $model->singularName),
			array('action' => 'delete', $id, 'model' => $this->params['model']),
			array('class' => 'btn btn-danger btn-large', 'escape' => false));
	} ?>
</div>

<h2><?php echo $displayField; ?></h2>

<table class="table table-striped table-bordered">
	<tbody>
		<?php foreach ($model->fields as $field => $data) { ?>

			<tr>
				<td>
					<b><?php echo $data['title']; ?></b>
				</td>
				<td>
					<?php echo $this->element('field', array(
						'field' => $field,
						'data' => $data
					)); ?>
				</td>
			</tr>

		<?php } ?>
	</tbody>
</table>