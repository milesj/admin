<?php
$params = $this->Paginator->params();

if ($params['count'] > 0) { ?>

	<nav class="pagination pagination-right">
		<div class="results">
			<?php echo $this->Paginator->counter(__d('admin', 'Showing %s-%s of %s', array(
				'<span>{:start}</span>',
				'<span>{:end}</span>',
				'<span>{:count}</span>'
			))); ?>
		</div>

		<ul>
			<?php echo $this->Paginator->numbers(array(
				'first' => __d('admin', 'First'),
				'last' => __d('admin', 'Last'),
				'currentTag' => 'span',
				'currentClass' => 'active',
				'separator' => '',
				'ellipsis' => '<li><span>...</span></li>',
				'tag' => 'li'
			)); ?>
		</ul>
	</nav>

<?php } ?>