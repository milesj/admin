<div class="action-buttons">
	<?php
	$links = array(
		'index' => array(
			'title' => __d('admin', 'Plugins'),
			'icon' => 'icon-paste'
		),
		'models' => array(
			'title' => __d('admin', 'Models'),
			'icon' => 'icon-file'
		),
		'config' => array(
			'title' => __d('admin', 'Configuration'),
			'icon' => 'icon-cog'
		),
		'cache' => array(
			'title' => __d('admin', 'Cache'),
			'icon' => 'icon-hdd'
		),
		'routes' => array(
			'title' => __d('admin', 'Routes'),
			'icon' => 'icon-road'
		),
		'logs' => array(
			'title' => __d('admin', 'Logs'),
			'icon' => 'icon-exchange',
			'url' => array('controller' => 'logs', 'action' => 'read', 'error')
		),
		/*'locales' => array(
			'title' => __d('admin', 'Locales'),
			'icon' => 'icon-globe'
		)*/
	);

	foreach ($links as $action => $link) {
		$class = 'btn btn-default';
		$url = array('controller' => 'admin', 'action' => $action);

		if ($this->action === $action) {
			$class .= ' active';
		}

		if (!empty($link['url'])) {
			$url = $link['url'];
		}

		echo $this->Html->link('<span class="' . $link['icon'] . '"></span> ' . $link['title'],
			$url,
			array('class' => $class, 'escape' => false));
	} ?>

	<span class="clear"></span>
</div>