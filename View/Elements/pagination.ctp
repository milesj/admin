<?php
$params = $this->Paginator->params();

if ($params['count'] > 0) { ?>

	<nav class="pagination pagination-right">
		<div class="results">
			<?php echo $this->Paginator->counter('Showing <span>{:start}</span>-<span>{:end}</span> of <span>{:count}</span>'); ?>
		</div>

		<ul>
			<?php echo $this->Paginator->numbers(array(
				'first' => 1,
				'last' => $params['pageCount'],
				'currentTag' => 'span',
				'currentClass' => 'active',
				'separator' => '',
				'ellipsis' => '<li><span>...</span></li>',
				'tag' => 'li'
			)); ?>
		</ul>
	</nav>

<?php } ?>