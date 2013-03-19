<table class="table table-striped table-bordered">
	<tbody>
		<?php foreach ($model->fields as $field => $data) { ?>

			<tr>
				<td class="span5">
					<b><?php echo $data['title']; ?></b>
				</td>
				<td>
					<?php echo $this->element('field', array(
						'result' => $result,
						'field' => $field,
						'data' => $data,
						'value' => $result[$model->alias][$field],
						'model' => $model
					)); ?>
				</td>
			</tr>

		<?php } ?>
	</tbody>
</table>