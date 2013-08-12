<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
	<nav class="breadcrumbs">
		<ul>
			<?php foreach ($crumbs as $i => $crumb) { ?>
				<li>
					<?php echo $this->Html->link($crumb['title'], $crumb['url']); ?>
				</li>
			<?php } ?>
		</ul>
	</nav>
<?php }