<?php
$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add(__('ACL'), array('controller' => 'acl', 'action' => 'index')); ?>

<div class="action-buttons">
	<?php
	echo $this->Html->link('<span class="icon-refresh icon-white"></span> ' . __('Sync'),
		array('controller' => 'acl', 'action' => 'sync',),
		array('class' => 'btn btn-info btn-large', 'escape' => false));

	echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add Requester'),
		array('controller' => 'crud', 'action' => 'create', 'model' => 'admin.request_object'),
		array('class' => 'btn btn-primary btn-large', 'escape' => false));

	echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add Controller'),
		array('controller' => 'crud', 'action' => 'create', 'model' => 'admin.control_object'),
		array('class' => 'btn btn-primary btn-large', 'escape' => false)); ?>
</div>

<h2><?php echo __('Access Control Lists'); ?></h2>

<table class="table table-striped table-bordered">
	<tbody>
		<tr>
			<td> </td>

			<?php foreach ($aros as $aro) { ?>

				<td class="matrix-head matrix-y" colspan="4">
					<b><?php echo $this->Html->link($aro['RequestObject']['alias'], array(
						'controller' => 'crud',
						'action' => 'read',
						'model' => 'admin.request_object',
						$aro['RequestObject']['id']
					)); ?></b>
				</td>

			<?php } ?>
		</tr>

		<?php foreach ($acos as $aco) { ?>

			<tr>
				<td class="matrix-head matrix-x">
					<b><?php echo $this->Html->link($aco['ControlObject']['alias'], array(
						'controller' => 'crud',
						'action' => 'read',
						'model' => 'admin.control_object',
						$aco['ControlObject']['id']
					)); ?></b>
				</td>

				<?php foreach ($aros as $aro) {
					$url = $this->Html->url(array(
						'action' => 'grant',
						'aro_id' => $aro['RequestObject']['id'],
						'aco_id' => $aco['ControlObject']['id']
					));

					if (isset($aro['ObjectPermission'][$aco['ControlObject']['id']])) {
						$permission = $aro['ObjectPermission'][$aco['ControlObject']['id']];
						$actionMap = array(
							'create' => 'icon-plus',
							'read' => 'icon-search',
							'update' => 'icon-edit',
							'delete' => 'icon-remove'
						);

						if ($permission['aro_id'] == $aro['RequestObject']['id']) {
							$url = $this->Html->url(array(
								'controller' => 'crud',
								'action' => 'read',
								'model' => 'admin.object_permission',
								$permission['id']
							));
						}

						foreach ($actionMap as $action => $icon) {
							$value = $permission['_' . $action];
							$tooltip = __(ucfirst($action)) . ': ';

							if ($value == 1) {
								$tooltip .= __('Has Access');
								$class = 'allow';

							} else if ($value == 0) {
								$tooltip .= __('Inherited from %s', $aro['Parent']['alias']);
								$class = 'inherit';

							} else if ($value == -1) {
								$tooltip .= __('Restricted Access');
								$class = 'deny';
							} ?>

						<td class="permission">
							<a href="<?php echo $url; ?>" class="action tip <?php echo $class; ?>" title="<?php echo $tooltip; ?>">
								<span class="<?php echo $icon; ?>"></span>
							</a>
						</td>

					<?php }
					} else { ?>

						<td colspan="4" class="permission">
							<?php if ($aco['ControlObject']['parent_id']) { ?>
								<a href="<?php echo $url; ?>" class="action tip" title="<?php echo __('No access defined. Grant permission?'); ?>">
									&nbsp;
								</a>
							<?php } ?>
						</td>

					<?php } ?>
				<?php } ?>
			</tr>

		<?php } ?>
	</tbody>
</table>
