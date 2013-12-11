<?php
$this->Breadcrumb->add(__d('admin', 'Routes'), array('controller' => 'admin', 'action' => 'routes'));

// Collapse arrays into a string
function formatArray($array) {
    $output = array();

    foreach ($array as $key => $value) {
        if (!$value) {
            continue;
        }

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        if (is_string($key)) {
            $value = '<span class="text-muted">' . $key . ':</span> ' . $value;
        }

        $output[] = $value;
    }

    return implode(', ', $output);
} ?>

<div class="title">
    <?php echo $this->element('admin/actions'); ?>

    <h2><?php echo __d('admin', 'Routes'); ?></h2>
</div>

<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th><?php echo __d('admin', 'Route'); ?></th>
                <th><?php echo __d('admin', 'Pattern'); ?></th>
                <th><?php echo __d('admin', 'Defaults'); ?></th>
                <th><?php echo __d('admin', 'Options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($routes as $route) { ?>

                <tr>
                    <td><?php echo $route->template; ?></td>
                    <td><?php echo $route->compile(); ?></td>
                    <td><?php echo formatArray($route->defaults); ?></td>
                    <td><?php echo formatArray($route->options); ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
</div>