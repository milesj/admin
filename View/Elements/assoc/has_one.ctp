<?php
$foreignModel = $this->Admin->introspect($assoc['className']);

if (empty($results[$foreignModel->primaryKey])) {
	return;
} ?>

<section class="has-one">
	<h5><?php echo $alias; ?> <span class="muted">(<?php echo $assoc['className']; ?>)</span></h5>

	<table class="table table-striped table-bordered table-hover clickable">
		<thead>
			<tr>
				<?php foreach ($foreignModel->fields as $field => $data) { ?>
					<th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<span><?php echo $data['title']; ?></span>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php foreach ($foreignModel->fields as $field => $data) { ?>

					<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<?php echo $this->element('field', array(
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
</section>