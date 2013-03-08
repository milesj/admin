<?php
$params = $this->Paginator->params();

if ($params['count'] > 0) { ?>

	<nav class="pagination pagination-right">
		<div class="results">
			<?php echo $this->Paginator->counter(__('Showing %s-%s of %s', array(
				'<span>{:start}</span>',
				'<span>{:end}</span>',
				'<span>{:count}</span>'
			))); ?>
		</div>

		<ul>
			<?php echo $this->Paginator->numbers(array(
				'first' => __('First'),
				'last' => __('Last'),
				'currentTag' => 'span',
				'currentClass' => 'active',
				'separator' => '',
				'ellipsis' => '<li><span>...</span></li>',
				'tag' => 'li'
			)); ?>
		</ul>
	</nav>

<?php } ?>