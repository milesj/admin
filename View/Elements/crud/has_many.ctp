<?php
$foreignModel = $this->Admin->introspect($assoc['className']);
$fields = $this->Admin->filterFields($foreignModel, $assoc['fields']); ?>

<div class="panel has-many">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php if (isset($counts[$alias])) {
				$total = $counts[$alias];
				$count = $assoc['limit'] ?: count($results);

				if ($count > $total) {
					$count = $total;
				} ?>

				<span class="text-muted pull-right"><?php echo __d('admin', '%s of %s', array($count, $total)); ?></span>
			<?php } ?>

			<?php echo $this->Admin->outputAssocName($foreignModel, $alias, $assoc['className']); ?>
		</h3>
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