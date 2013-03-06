<?php
$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add(__('ACL'), array('controller' => 'acl', 'action' => 'index')); ?>

<h2><?php echo __('Access Control Lists'); ?></h2>

<table class="table table-striped table-bordered">
	<tbody>
		<tr>
			<td> </td>

			<?php foreach ($aros as $aro) { ?>

				<td class="matrix-head matrix-y" colspan="4">
					<b><?php echo $this->Html->link($aro['Aro']['alias'], array(
						'controller' => 'crud',
						'action' => 'read',
						'model' => 'aro',
						$aro['Aro']['id']
					)); ?></b>
				</td>

			<?php } ?>
		</tr>

		<?php foreach ($acos as $aco) { ?>

			<tr>
				<td class="matrix-head matrix-x">
					<b><?php echo $this->Html->link($aco['Aco']['alias'], array(
						'controller' => 'crud',
						'action' => 'read',
						'model' => 'aco',
						$aco['Aco']['id']
					)); ?></b>
				</td>

				<?php foreach ($aros as $aro) {
					if (isset($aro['Permission'][$aco['Aco']['id']])) {
						$permission = $aro['Permission'][$aco['Aco']['id']];
						$actionMap = array(
							'create' => 'icon-plus',
							'read' => 'icon-search',
							'update' => 'icon-edit',
							'delete' => 'icon-remove'
						);

						$url = $this->Html->url(array(
							'controller' => 'crud',
							'action' => 'read',
							'model' => 'permission',
							$permission['id']
						));

						foreach ($actionMap as $action => $icon) {
							$value = $permission['_' . $action];
							$tooltip = __(ucfirst($action)) . ': ';

							if ($value == 1) {
								$tooltip .= __('Has Access');
								$class = 'allow';

							} else if ($value == 0) {
								$tooltip .= __('Inherited From %s', $aro['Parent']['alias']);
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
							<a href="javascript:;" class="action tip missing" title="<?php echo __('No Access Defined'); ?>">
								&nbsp;
							</a>
						</td>

					<?php } ?>
				<?php } ?>
			</tr>

		<?php } ?>
	</tbody>
</table>
