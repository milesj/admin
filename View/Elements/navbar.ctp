<?php
$controller = $this->request->controller;
$pluginParam = null;
$modelParam = $this->request->model;

if ($modelParam) {
	list($pluginParam, $modelParam) = pluginSplit($modelParam);
} ?>

<div class="head navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a data-target=".navbar-inverse-collapse" data-toggle="collapse" class="btn btn-navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<?php $navTitle = $config['Admin']['appName'];

			if (env('REMOTE_ADDR') === '127.0.0.1') {
				$navTitle .= ' <span class="text-error">[dev]</span>';
			} else {
				$navTitle .= ' <span class="text-success">[prod]</span>';
			}

			echo $this->Html->link($navTitle, array(
				'controller' => 'admin',
				'action' => 'index'
			), array('class' => 'brand', 'escape' => false)); ?>

			<div class="nav-collapse navbar-inverse-collapse navbar-responsive-collapse collapse">
				<ul class="nav pull-right">
					<li class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle user" href="javascript:;">
							<?php if (!empty($user[$config['User']['fieldMap']['avatar']])) {
								echo $this->Html->image($user[$config['User']['fieldMap']['avatar']], array('class' => 'avatar'));
							} ?>

							<span class="name">
								<?php echo $user[$config['User']['fieldMap']['username']]; ?>
								<span class="caret"></span>
							</span>
						</a>

						<ul class="dropdown-menu">
							<li><?php echo $this->Html->link(__d('admin', 'View Site'), '/'); ?></li>
							<?php if ($profileRoute = $this->Admin->getUserRoute('profile', $user)) { ?>
								<li><?php echo $this->Html->link(__d('admin', 'View Profile'), $profileRoute); ?></li>
							<?php }
							if ($settingsRoute = $config['User']['routes']['settings']) { ?>
								<li><?php echo $this->Html->link(__d('admin', 'Settings'), $settingsRoute); ?></li>
							<?php } ?>
							<li><?php echo $this->Html->link(__d('admin', 'Logout'), $config['User']['routes']['logout']); ?></li>
						</ul>
					</li>

					<!-- Spacing for DebugKit -->
					<li class="divider-vertical"></li>
				</ul>

				<ul class="nav">
					<?php // Loop top-level menu
					$currentRoute = Router::currentRoute();
					$currentSection = empty($currentRoute->options['section']) ? null : $currentRoute->options['section'];

					foreach (Configure::read('Admin.menu') as $section => $menu) { ?>

						<li<?php echo ($currentSection === $section) ? ' class="active"' : ''; ?>>
							<?php
							$title = $menu['title'];

							if ($section === 'reports' && !empty($pendingReports)) {
								$title .= ' <span class="label label-important">' . $pendingReports . '</span>';
							}

							echo $this->Html->link($title, $menu['url'], array('escape' => false)); ?>
						</li>

					<?php } ?>

					<li class="divider-vertical"></li>

					<?php // Loop model menu
					foreach ($this->Admin->getNavigation() as $plugin => $groups) {
						if (empty($groups)) {
							continue;
						} ?>

						<li class="dropdown<?php echo (mb_strtolower($plugin) === $pluginParam) ? ' active' : ''; ?>">
							<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">
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
		</div>
	</div>
</div>