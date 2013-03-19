<?php
$this->Breadcrumb->add(__('Logs'), array('controller' => 'logs', 'action' => 'index'));
$this->Breadcrumb->add(__('System'), array('controller' => 'logs', 'action' => 'read', $type));
$this->Breadcrumb->add(Inflector::humanize($type), array('controller' => 'logs', 'action' => 'read', $type));

echo $this->element('logs/actions'); ?>

<h2><?php echo __('System Logs'); ?></h2>

<table id="table" class="table table-striped table-bordered sortable">
	<thead>
		<tr>
			<th><span><?php echo __('Code'); ?></span></th>
			<th><span><?php echo __('Exception'); ?></span></th>
			<th><span><?php echo __('Message'); ?></span></th>
			<th><span><?php echo __('Count'); ?></span></th>
			<th><span><?php echo __('Date'); ?></span></th>
		</tr>
	</thead>
	<tbody>
		<?php if ($logs) {
			foreach ($logs as $i => $log) { ?>

			<tr>
				<td class="type-integer">
					<?php
					$class = null;
					$text = null;

					// Via fatal
					if (mb_stripos($log['message'], 'fatal') !== false) {
						$class = 'label-important';
						$text = 'FATAL';

					// Via exception
					} else if ($log['exception']) {
						$ex = new $log['exception'](null);
						$text = $ex->getCode();

						// Hide 0 status codes
						if ($text == 0) {
							$text = null;
						}

						if ($ex instanceof HttpException) {
							$class = 'label-warning';

						} else if ($ex instanceof FatalErrorException) {
							$class = 'label-important';
							$text = 'FATAL';

						} else if ($ex instanceof CakeException) {
							$class = 'label-info';
						}
					}

					if ($text) { ?>

						<span class="label <?php echo $class; ?>"><?php echo $text; ?></span>

					<?php } ?>
				</td>
				<td><?php echo $log['exception']; ?></td>
				<td>
					<?php if (!$log['stack']) {
						echo $log['message'];
					} else { ?>
						<a href="javascript:;" onclick="$('#stack-<?php echo $i; ?>').toggle();"><?php echo $log['message']; ?></a>

						<div id="stack-<?php echo $i; ?>" class="muted" style="display: none;">
							<?php echo implode('<br>', $log['stack']); ?>
						</div>
					<?php } ?>
				</td>
				<td class="type-integer"><?php echo $log['count']; ?></td>
				<td class="type-datetime"><?php echo $log['date']; ?></td>
			</tr>

			<?php }
		} else { ?>

			<tr>
				<td colspan="5" class="no-results">
					<?php echo __('No results to display'); ?>
				</td>
			</tr>

		<?php } ?>
	</tbody>
</table>