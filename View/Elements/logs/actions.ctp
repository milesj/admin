<div class="action-buttons">
	<?php
	foreach (CakeLog::configured() as $stream) {
		$class = 'btn btn-default';

		if (isset($type) && $stream == $type) {
			$class .= ' active';
		}

		echo $this->Html->link(Inflector::humanize($stream), array('action' => 'read', $stream), array('class' => $class));
	} ?>
</div>