<?php
// Flatten an array once a specific depth is reached
$flatten = function($array) {
	$output = array();

	foreach ($array as $key => $value) {
		$return = $key . ': ';

		if (is_array($value)) {
			$return .= '[' . $flatten($value) . ']';
		} else {
			$return .= '"' . $value . '"';
		}

		$output[] = $return;
	}

	return implode('<br>', $output);
}; ?>

<table class="table table-bordered table-striped">
	<tbody>
		<?php foreach ($data as $key => $value) { ?>
			<tr>
				<td><b><?php echo $key; ?></b></td>
				<td>
					<?php if (is_bool($value)) { ?>

						<span class="text-error"><?php echo $value ? 'true' : 'false'; ?></span>

					<?php } else if (is_numeric($value)) { ?>

						<span class="text-warning"><?php echo $value; ?></span>

					<?php } else if (empty($value)) { ?>

						<span class="muted">(empty)</span>

					<?php } else if (is_string($value)) { ?>

						<span class="text-success"><?php echo h($value); ?></span>

					<?php } else if (is_array($value)) {
						// List of values
						if (Hash::numeric(array_keys($value))) { ?>

							<span class="text-info"><?php echo implode(', ', $value); ?></span>

						<?php // Hash map
						} else if ($depth > 0) {
							echo $this->element('admin/config', array(
								'data' => $value,
								'parent' => $parent . $key . '.',
								'depth' => ($depth + 1)
							));

						// Display table in modal
						} else {
							$id = rand(); ?>

							<a href="#modal-<?php echo $id; ?>" data-toggle="modal">
								<?php echo __('View'); ?>
								<span class="icon-external-link" style="font-size: 10px"></span>
							</a>

							<div id="modal-<?php echo $id; ?>" class="modal hide">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3><?php echo $parent . $key; ?></h3>
								</div>

								<div class="modal-body">
									<?php echo $this->element('admin/config', array(
										'data' => $value,
										'parent' => $parent . $key . '.',
										'depth' => ($depth + 1)
									)); ?>
								</div>
							</div>

						<?php }
					} ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>