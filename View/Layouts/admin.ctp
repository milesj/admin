<?php
echo $this->Html->docType(); ?>
<html lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle($config['appName'], array('reverse' => true)); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	echo $this->Html->css('Admin.bootstrap.min');
	echo $this->Html->css('Admin.bootstrap-responsive.min');
	echo $this->Html->css('Admin.style');
	echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	echo $this->Html->script('Admin.bootstrap.min');
	echo $this->Html->script('Admin.admin'); ?>
</head>
<body class="controller-<?php echo $this->params['controller']; ?> action-<?php echo $this->action; ?>">
	<?php echo $this->element('navbar'); ?>

	<div class="body container-fluid">
		<div class="row-fluid">
			<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
				<div class="well well-small breadcrumbs">
					<ul class="breadcrumb">
						<?php foreach ($crumbs as $crumb) { ?>
							<li>
								<?php echo $this->Html->link($crumb['title'], $crumb['url']); ?>
								<span class="divider">&raquo;</span>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php }

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