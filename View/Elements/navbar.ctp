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

			<?php echo $this->Html->link($config['appName'], array(
				'controller' => 'admin',
				'action' => 'index'
			), array('class' => 'brand')); ?>

			<div class="nav-collapse collapse navbar-inverse-collapse">
				<ul class="nav">
					<li<?php echo ($controller === 'acl') ? ' class="active"' : ''; ?>>
						<?php echo $this->Html->link(__('ACL'), array(
							'plugin' => 'admin',
							'controller' => 'acl',
							'action' => 'index'
						)); ?>
					</li>

					<li<?php echo ($controller === 'logs') ? ' class="active"' : ''; ?>>
						<?php echo $this->Html->link(__('Logs'), array(
							'plugin' => 'admin',
							'controller' => 'logs',
							'action' => 'index'
						)); ?>
					</li>

					<li<?php echo ($controller === 'reports') ? ' class="active"' : ''; ?>>
						<?php
						$title = __('Reports');

						if ($pendingReports) {
							$title .= ' <span class="label label-important">' . $pendingReports . '</span>';
						}

						echo $this->Html->link($title, array(
							'plugin' => 'admin',
							'controller' => 'reports',
							'action' => 'index'
						), array('escape' => false)); ?>
					</li>

					<li class="divider-vertical"></li>

					<?php foreach ($this->Admin->getNavigation() as $plugin => $groups) {
						if (empty($groups)) {
							continue;
						} ?>

						<li class="dropdown<?php echo (strtolower($plugin) === $pluginParam) ? ' active' : ''; ?>">
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