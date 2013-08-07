<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
	<nav class="breadcrumbs">
		<ul class="breadcrumb">
			<?php foreach ($crumbs as $i => $crumb) {
				if (empty($crumbs[$i + 1])) { ?>
					<li class="active">
						<?php echo $crumb['title']; ?>
					</li>
				<?php } else { ?>
					<li>
						<?php echo $this->Html->link($crumb['title'], $crumb['url']); ?>
					</li>
				<?php }
			} ?>
		</ul>
	</nav>
<?php }