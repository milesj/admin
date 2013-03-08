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
					<li>
						<?php echo $this->Html->link(__('ACL'), array(
							'plugin' => 'admin',
							'controller' => 'acl',
							'action' => 'index'
						)); ?>
					</li>

					<?php foreach ($this->Admin->getNavigation() as $plugin => $groups) {
						if (empty($groups)) {
							continue;
						} ?>

						<li class="dropdown">
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
											<?php echo $this->Html->link($model['title'], array(
												'plugin' => 'admin',
												'controller' => 'crud',
												'action' => 'index',
												'model' => $model['url']
											)); ?>
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
														<?php echo $this->Html->link($model['title'], array(
															'plugin' => 'admin',
															'controller' => 'crud',
															'action' => 'index',
															'model' => $model['url']
														)); ?>
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