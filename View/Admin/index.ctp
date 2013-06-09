<?php if (!$this->Session->check('Admin')) { ?>
	<div class="alert alert-danger">
		<span class="icon-warning-sign"></span> &nbsp;
		<?php echo __d('admin', 'ACL permissions do not exist within the session and all access has been disabled.'); ?>
		<a href="http://milesj.me/code/cakephp/admin#faq"><?php echo __d('admin', 'Please update session management and re-login.'); ?></a>
	</div>
<?php }

echo $this->element('admin/actions'); ?>

<h2><?php echo __d('admin', 'Plugins'); ?></h2>

<div class="row-fluid" id="grid">
	<?php foreach ($plugins as $plugin) { ?>

		<div id="<?php echo $plugin['slug']; ?>" class="well">
			<h3><?php echo $plugin['title']; ?></h3>

			<ul class="nav nav-pills nav-stacked">
				<?php foreach ($plugin['models'] as $model) {
					$url = $this->Html->url(array(
						'controller' => 'crud',
						'action' => 'index',
						'model' => $model['url']
					)); ?>

					<li>
						<?php if ($model['installed']) {
							echo $this->Html->link('<span class="icon-plus"></span>', array(
								'controller' => 'crud',
								'action' => 'create',
								'model' => $model['url']
							), array(
								'title' => __d('admin', 'Add'),
								'class' => 'pull-right tip',
								'escape' => false
							));
						} ?>

						<a href="<?php echo $url; ?>">
							<?php echo $this->Admin->outputIconTitle($model); ?>

							<?php if (!$model['installed']) { ?>
								<span class="label label-important tip" title="<?php echo __d('admin', 'Not Installed'); ?>">&nbsp;!&nbsp;</span>
							<?php } else { ?>
								<span class="muted">(<?php echo number_format($counts[$model['class']]); ?>)</span>
							<?php } ?>
						</a>
					</li>

				<?php } ?>
			</ul>
		</div>

	<?php } ?>
</div>