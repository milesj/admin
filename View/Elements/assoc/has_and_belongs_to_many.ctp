<?php
$foreignModel = $this->Admin->introspect($assoc['className']);
$withModel = $this->Admin->introspect($assoc['with']);
$fields = $this->Admin->filterFields($withModel); ?>

<section class="has-and-belongs-to-many">
	<h5>
		<?php echo $alias; ?> <span class="muted">(<?php echo $assoc['className']; ?>)</span> &rarr;
		<?php echo $withModel->alias; ?> <span class="muted">(<?php echo $assoc['with']; ?>)</span>
	</h5>

	<table class="table table-striped table-bordered table-hover clickable">
		<thead>
			<tr>
				<th>
					<span><?php echo $alias; ?></span>
				</th>

				<?php foreach ($fields as $field => $data) {
					if (strpos($field, '_id') !== false) {
						continue;
					} ?>

					<th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
						<span><?php echo $data['title']; ?></span>
					</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results as $result) { ?>

				<tr>
					<td>
						<?php echo $this->Html->link($result[$foreignModel->displayField], array(
							'action' => 'read',
							'model' => $foreignModel->urlSlug,
							$result[$foreignModel->primaryKey]
						)); ?>
					</td>

					<?php foreach ($fields as $field => $data) {
						if (strpos($field, '_id') !== false) {
							continue;
						} ?>

						<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
							<?php echo $this->element('field', array(
								'result' => $result,
								'field' => $field,
								'data' => $data,
								'value' => $result[$withModel->alias][$field],
								'model' => $withModel
							)); ?>
						</td>

					<?php } ?>
				</tr>

			<?php } ?>
		</tbody>
	</table>
</section>