<?php
echo $this->Html->docType(); ?>
<html lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle('Admin'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	echo $this->Html->css('Admin.bootstrap.min');
	echo $this->Html->css('Admin.bootstrap-responsive.min');
	echo $this->Html->css('Admin.style');
	echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	echo $this->Html->script('Admin.bootstrap.min'); ?>
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">

			<!-- Left Column -->
			<div class="span2">
				<div class="well sidebar-nav">
					<ul class="nav nav-list">
						<?php foreach ($this->Admin->getNavigation() as $plugin => $models) { ?>
							<li class="nav-header"><?php echo $plugin; ?></li>

							<?php foreach ($models as $model) { ?>
								<li>
									<?php echo $this->Html->link($model['model'], array(
										'plugin' => 'admin',
										'controller' => 'crud',
										'action' => 'index',
										'model' => $model['url']
									)); ?>
								</li>
						<?php } } ?>
					</ul>
				</div>
			</div>

			<!-- Right Column -->
			<div class="span10">
				<?php echo $this->fetch('content'); ?>
			</div>

		</div>
	</div>
</body>
</html>