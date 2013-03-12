<div class="action-buttons">
	<?php
	$links = array(
		'index' => array(
			'title' => __('Plugins'),
			'icon' => 'icon-paste'
		),
		'models' => array(
			'title' => __('Models'),
			'icon' => 'icon-file'
		),
		'config' => array(
			'title' => __('Configuration'),
			'icon' => 'icon-cog'
		)
	);

	foreach ($links as $action => $link) {
		$class = 'btn btn-large';

		if ($this->action === $action) {
			$class .= ' btn-inverse';
		}

		echo $this->Html->link('<span class="' . $link['icon'] . '"></span> ' . $link['title'],
			array('controller' => 'admin', 'action' => $action),
			array('class' => $class, 'escape' => false));
	} ?>

	<span class="clear"></span>
</div>