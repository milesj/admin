<?php
echo $this->Html->docType(); ?>
<html lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle($config['appName'], array('reverse' => true, 'depth' => 3)); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	echo $this->Html->css('Admin.bootstrap.min');
	echo $this->Html->css('Admin.bootstrap-responsive.min');
	echo $this->Html->css('Admin.style');
	echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	echo $this->Html->script('Admin.bootstrap.min');
	echo $this->Html->script('Admin.admin'); ?>
</head>
<body class="action-<?php echo $this->action; ?>">
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
						<?php
						$navigation = $this->Admin->getNavigation();

						// No plugins, so show all models
						if (count($navigation) === 1) {
							$navigation = array_values($navigation);

							foreach ($navigation[0] as $model) { ?>

								<li>
									<?php echo $this->Html->link($model['model'], array(
										'plugin' => 'admin',
										'controller' => 'crud',
										'action' => 'index',
										'model' => $model['url']
									)); ?>
								</li>

							<?php }

						// Or group by plugin
						} else {
							foreach ($navigation as $plugin => $models) { ?>

								<li class="dropdown">
									<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">
										<?php echo $plugin; ?>
										<span class="caret"></span>
									</a>

									<ul class="dropdown-menu">
										<?php foreach ($models as $model) { ?>
											<li>
												<?php echo $this->Html->link($model['model'], array(
													'plugin' => 'admin',
													'controller' => 'crud',
													'action' => 'index',
													'model' => $model['url']
												)); ?>
											</li>
										<?php } ?>
									</ul>
								</li>

							<?php }
						} ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="body container-fluid">
		<div class="row-fluid">
			<div class="well well-small breadcrumbs">
				<ul class="breadcrumb">
					<?php foreach ($this->Breadcrumb->get() as $crumb) { ?>
						<li>
							<?php echo $this->Html->link($crumb['title'], $crumb['url']); ?>
							<span class="divider">&raquo;</span>
						</li>
					<?php } ?>
				</ul>
			</div>

			<?php
			echo $this->Session->flash();
			echo $this->fetch('content'); ?>
		</div>
	</div>

	<footer class="foot">
		<div class="copyright">
			<?php printf(__d('forum', 'Powered by the %s v%s'), $this->Html->link('Admin Plugin', 'http://milesj.me/code/cakephp/admin'), strtoupper($config['version'])); ?><br>
			<?php printf(__d('forum', 'Created by %s'), $this->Html->link('Miles Johnson', 'http://milesj.me')); ?>
		</div>

		<?php if (!CakePlugin::loaded('DebugKit')) {
			echo $this->element('sql_dump');
		} ?>
	</footer>
</body>
</html>