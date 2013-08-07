<?php
$foreignModel = $this->Admin->introspect($assoc['className']);
$fields = $this->Admin->filterFields($foreignModel, $assoc['fields']);

if (empty($results[$foreignModel->primaryKey])) {
	return;
} ?>

<div class="panel has-one">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $this->Admin->outputAssocName($foreignModel, $alias, $assoc['className']); ?></h3>
	</div>

	<table class="table table-striped table-bordered table-hover clickable">
		<thead>
			<tr>
				<?php foreach ($fields as $field => $data) { ?>
					<th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<span><?php echo $data['title']; ?></span>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php foreach ($fields as $field => $data) { ?>

					<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<?php echo $this->element('Admin.field', array(
							'result' => $results,
							'field' => $field,
							'data' => $data,
							'value' => $results[$field],
							'model' => $foreignModel
						)); ?>
					</td>

				<?php } ?>
			</tr>
		</tbody>
	</table>
</div>