<?php
$controller = $this->request->controller;
$modelParam = $this->request->model;
$pluginParam = null;

if ($modelParam) {
	list($pluginParam, $modelParam) = pluginSplit($modelParam);
} ?>

<div class="navbar navbar-inverse navbar-fixed-top">
	<?php // Brand name
	$navTitle = $config['Admin']['appName'];

	if (env('REMOTE_ADDR') === '127.0.0.1') {
		$navTitle .= ' <span class="text-danger">[dev]</span>';
	} else {
		$navTitle .= ' <span class="text-success">[prod]</span>';
	}

	echo $this->Html->link($navTitle, array(
		'controller' => 'admin',
		'action' => 'index'
	), array('class' => 'navbar-brand', 'escape' => false)); ?>

	<div class="nav-buttons pull-right">
		<div class="btn-group">
			<button type="button" class="btn btn-info navbar-btn dropdown-toggle" data-toggle="dropdown">
				<?php if (!empty($user[$config['User']['fieldMap']['avatar']])) {
					echo $this->Html->image($user[$config['User']['fieldMap']['avatar']], array('class' => 'avatar'));
				} ?>

				<?php echo $user[$config['User']['fieldMap']['username']]; ?>
				<span class="caret"></span>
			</button>

			<ul class="dropdown-menu">
				<li><?php echo $this->Html->link(__d('admin', 'View Site'), '/'); ?></li>
				<?php
				if ($profileRoute = $this->Admin->getUserRoute('profile', $user)) { ?>
					<li><?php echo $this->Html->link(__d('admin', 'View Profile'), $profileRoute); ?></li>
				<?php }
				if ($settingsRoute = $this->Admin->getUserRoute('settings', $user)) { ?>
					<li><?php echo $this->Html->link(__d('admin', 'Settings'), $settingsRoute); ?></li>
				<?php } ?>
			</ul>
		</div>

		<?php echo $this->Html->link(__d('admin', 'Logout'), $config['User']['routes']['logout'], array('class' => 'btn btn-danger navbar-btn')); ?>
	</div>

	<ul class="nav navbar-nav">
		<?php // Loop top-level menu
		$currentRoute = Router::currentRoute();
		$currentSection = empty($currentRoute->options['section']) ? null : $currentRoute->options['section'];

		foreach (Configure::read('Admin.menu') as $section => $menu) { ?>

			<li<?php echo ($currentSection === $section) ? ' class="active"' : ''; ?>>
				<?php
				$title = $menu['title'];

				if ($section === 'reports' && !empty($pendingReports)) {
					$title .= ' <span class="label label-danger">' . $pendingReports . '</span>';
				}

				echo $this->Html->link($title, $menu['url'], array('escape' => false)); ?>
			</li>

		<?php }

		// Loop model menu
		foreach ($this->Admin->getNavigation() as $plugin => $groups) {
			if (empty($groups)) {
				continue;
			} ?>

			<li class="dropdown<?php echo (strtolower($plugin) === $pluginParam) ? ' active' : ''; ?>">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<?php echo $plugin; ?>
					<span class="caret"></span>
				</a>

				<ul class="dropdown-menu">
					<?php // Single group
					if (count($groups) == 1) {
						$groups = array_values($groups);

						foreach ($groups[0] as $model) { ?>

							<li>
								<?php echo $this->Html->link($this->Admin->outputIconTitle($model), array(
									'plugin' => 'admin',
									'controller' => 'crud',
									'action' => 'index',
									'model' => $model['url']
								), array('escape' => false)); ?>
							</li>

						<?php }

					// Multiple groups
					} else {
						foreach ($groups as $group => $models) { ?>

							<li class="dropdown-submenu">
								<a href="javascript:;" tabindex="-1"><?php echo $group; ?></a>

								<ul class="dropdown-menu">
									<?php foreach ($models as $model) { ?>

										<li>
											<?php echo $this->Html->link($this->Admin->outputIconTitle($model), array(
												'plugin' => 'admin',
												'controller' => 'crud',
												'action' => 'index',
												'model' => $model['url']
											), array('escape' => false)); ?>
										</li>

									<?php } ?>
								</ul>
						  </li>

					<?php } } ?>
				</ul>
			</li>

		<?php } ?>
	</ul>
</div>