<?php
$foreignModel = $this->Admin->introspect(($model->plugin ? $model->plugin . '.' : '') . $assoc['className']); // a best-guess that associated model shares plugin
$fields = $this->Admin->filterFields($foreignModel, $assoc['fields']); ?>

<div class="panel has-many">
	<div class="panel-head">
		<h5>
			<?php if (isset($counts[$alias])) {
				$total = $counts[$alias];
				$count = $assoc['limit'] ?: count($results);

				if ($count > $total) {
					$count = $total;
				} ?>

				<span class="text-muted pull-right"><?php echo __d('admin', '%s of %s', array($count, $total)); ?></span>
			<?php } ?>

			<?php echo $this->Admin->outputAssocName($foreignModel, $alias, $assoc['className']); ?>
		</h5>
	</div>

	<div class="panel-body">
		<div class="action-buttons">
			<?php
			if ($this->Admin->hasAccess($foreignModel->qualifiedName, 'create')) {
				echo $this->Html->link('<span class="icon-pencil icon-white"></span> ' . __d('admin', 'Add %s', $foreignModel->singularName),
					array('action' => 'create', 'model' => $foreignModel->urlSlug, $assoc['foreignKey'] => $result[$model->alias][$model->primaryKey]),
					array('class' => 'button is-info', 'escape' => false));
			}
			?>
		</div>
		<table class="table table--hover table--clickable">
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
				<?php foreach ($results as $result) { ?>

					<tr>
						<?php foreach ($fields as $field => $data) { ?>

							<td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
								<?php echo $this->element('Admin.field', array(
									'result' => $result,
									'field' => $field,
									'data' => $data,
									'value' => $result[$field],
									'model' => $foreignModel
								)); ?>
							</td>

						<?php } ?>
					</tr>

				<?php } ?>
			</tbody>
		</table>
	</div>
</div>