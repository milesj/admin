<nav class="pagination pagination--grouped round <?php echo $class; ?>">
    <ul>
        <?php echo $this->Paginator->numbers(array(
            'first' => __d('admin', 'First'),
            'last' => __d('admin', 'Last'),
            'currentTag' => 'a',
            'currentClass' => 'is-active',
            'separator' => '',
            'ellipsis' => '<li><span>...</span></li>',
            'tag' => 'li'
        )); ?>
    </ul>

    <div class="counter">
        <?php echo $this->Paginator->counter(__d('admin', 'Showing %s-%s of %s', array(
            '<span>{:start}</span>',
            '<span>{:end}</span>',
            '<span>{:count}</span>'
        ))); ?>
    </div>

    <script type="text/javascript">
        $(function() {
            // Add button class to pagination links since CakePHP doesn't support it
            $('.pagination a').addClass('button');
        });
    </script>
</nav>